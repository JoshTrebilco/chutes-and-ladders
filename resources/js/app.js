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

    // Array of handlers for each event type
    handlers: {
        rolledDice: [],
        playerMoved: [],
        endedTurn: [],
        allEventsComplete: []
    },

    // Function to check if all events are complete
    checkEventSequence: function() {
        if (this.eventSequence.rolledDice && this.eventSequence.playerMoved && this.eventSequence.endedTurn) {
            console.log('All events complete');
            this.handlers.allEventsComplete.forEach(handler => handler());
        }
    },

    // Function to reset event sequence for new turn
    resetEventSequence: function() {
        this.eventSequence.rolledDice = false;
        this.eventSequence.playerMoved = false;
        this.eventSequence.endedTurn = false;
    },

    // Register handlers for events
    onRolledDice: function(handler) {
        this.handlers.rolledDice.push(handler);
    },

    onPlayerMoved: function(handler) {
        this.handlers.playerMoved.push(handler);
    },

    onEndedTurn: function(handler) {
        this.handlers.endedTurn.push(handler);
    },

    onAllEventsComplete: function(handler) {
        this.handlers.allEventsComplete.push(handler);
    },

    // Initialize event listeners
    init: function() {
        const channel = window.Echo.channel('test-channel');
        
        channel.listen('BroadcastEvent', data => {
            if (data.event == 'App\\Events\\Gameplay\\RolledDice' && data.gameState?.last_roll !== undefined) {
                // Reset event sequence for new roll
                this.resetEventSequence();
                this.eventSequence.gameId = data.gameState.id;
                
                // Call all rolledDice handlers
                const promises = this.handlers.rolledDice.map(handler => handler(data));
                Promise.all(promises).then(() => {
                    this.eventSequence.rolledDice = true;
                    this.checkEventSequence();
                });
            }
            
            if (data.event == 'App\\Events\\Gameplay\\PlayerMoved') {
                // Call all playerMoved handlers
                const promises = this.handlers.playerMoved.map(handler => handler(data));
                Promise.all(promises).then(() => {
                    this.eventSequence.playerMoved = true;
                    this.checkEventSequence();
                });
            }
            
            if (data.event == 'App\\Events\\Gameplay\\EndedTurn') {
                // Call all endedTurn handlers
                this.handlers.endedTurn.forEach(handler => handler(data));
                this.eventSequence.endedTurn = true;
                this.checkEventSequence();
            }
        });
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.GameEventManager.init();
});
