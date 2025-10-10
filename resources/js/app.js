import './bootstrap';

import './echo';

// Shared event handling logic for game components
window.GameEventManager = {
    // Event handling state
    eventSequence: {
        rolledDice: false,
        playerMoved: false,
        endedTurn: false,
        gameId: null
    },

    // Function to check if all events are complete
    checkEventSequence: function() {
        if (this.eventSequence.rolledDice && this.eventSequence.playerMoved && this.eventSequence.endedTurn) {
            console.log('All events complete');
            this.onAllEventsComplete();
        }
    },

    // Function to reset event sequence for new turn
    resetEventSequence: function() {
        this.eventSequence.rolledDice = false;
        this.eventSequence.playerMoved = false;
        this.eventSequence.endedTurn = false;
    },

    // Override this method in components that need custom completion handling
    onAllEventsComplete: function() {
        // Default: do nothing, let components override
    },

    // Initialize event listeners
    init: function() {
        const channel = window.Echo.channel('test-channel');
        
        channel.listen('BroadcastEvent', data => {
            if (data.event == 'App\\Events\\Gameplay\\RolledDice' && data.gameState?.last_roll !== undefined) {
                // Reset event sequence for new roll
                this.resetEventSequence();
                this.eventSequence.gameId = data.gameState.id;
                this.onRolledDice(data).then(() => {
                    this.eventSequence.rolledDice = true;
                    this.checkEventSequence();
                });
            }
            
            if (data.event == 'App\\Events\\Gameplay\\PlayerMoved') {
                this.eventSequence.playerMoved = true;
                this.onPlayerMoved(data).then(() => {
                    this.checkEventSequence();
                });
            }
            
            if (data.event == 'App\\Events\\Gameplay\\EndedTurn') {
                this.eventSequence.endedTurn = true;
                this.onEndedTurn(data);
                this.checkEventSequence();
            }
        });
    },

    // Override these methods in components for custom handling
    onRolledDice: function(data) {
        // Default: return resolved promise immediately
        return Promise.resolve();
    },

    onPlayerMoved: function(data) {
        // Default: return resolved promise
        return Promise.resolve();
    },

    onEndedTurn: function(data) {
        // Default: do nothing
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.GameEventManager.init();
});
