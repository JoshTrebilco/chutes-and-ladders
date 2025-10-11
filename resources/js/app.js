import './bootstrap';
import './echo';

/**
 * Event type constants to avoid magic strings
 */
const EVENT_TYPES = {
    ROLLED_DICE: 'App\\Events\\Gameplay\\RolledDice',
    PLAYER_MOVED: 'App\\Events\\Gameplay\\PlayerMoved',
    ENDED_TURN: 'App\\Events\\Gameplay\\EndedTurn'
};

/**
 * GameEventManager - Handles game event sequencing and broadcasting
 * Manages the flow of game events and ensures proper sequencing
 */
class GameEventManager {
    constructor() {
        this.eventSequence = {
            rolledDice: false,
            playerMoved: false,
            endedTurn: false,
            gameId: null
        };

        this.handlers = {
            rolledDice: [],
            playerMoved: [],
            endedTurn: [],
            allEventsComplete: []
        };
    }

    /**
     * Checks if all events in the current sequence are complete
     */
    checkEventSequence() {
        const { rolledDice, playerMoved, endedTurn } = this.eventSequence;
        
        if (rolledDice && playerMoved && endedTurn) {
            console.log('All events complete');
            this.handlers.allEventsComplete.forEach(handler => handler());
        }
    }

    /**
     * Resets the event sequence for a new turn
     */
    resetEventSequence() {
        this.eventSequence.rolledDice = false;
        this.eventSequence.playerMoved = false;
        this.eventSequence.endedTurn = false;
    }

    /**
     * Registers a handler for the rolled dice event
     * @param {Function} handler - Function to call when dice are rolled
     */
    onRolledDice(handler) {
        this.handlers.rolledDice.push(handler);
    }

    /**
     * Registers a handler for the player moved event
     * @param {Function} handler - Function to call when a player moves
     */
    onPlayerMoved(handler) {
        this.handlers.playerMoved.push(handler);
    }

    /**
     * Registers a handler for the ended turn event
     * @param {Function} handler - Function to call when a turn ends
     */
    onEndedTurn(handler) {
        this.handlers.endedTurn.push(handler);
    }

    /**
     * Registers a handler for when all events are complete
     * @param {Function} handler - Function to call when all events are done
     */
    onAllEventsComplete(handler) {
        this.handlers.allEventsComplete.push(handler);
    }

    /**
     * Executes handlers for a specific event type with error handling
     * @param {string} eventType - The type of event handlers to execute
     * @param {Object} data - Event data to pass to handlers
     * @param {boolean} usePromises - Whether to use Promise.all for async handling
     */
    async executeHandlers(eventType, data, usePromises = false) {
        const handlers = this.handlers[eventType];
        
        if (!handlers.length) return;

        try {
            if (usePromises) {
                const promises = handlers.map(handler => handler(data));
                await Promise.all(promises);
            } else {
                handlers.forEach(handler => handler(data));
            }
        } catch (error) {
            console.error(`Error executing ${eventType} handlers:`, error);
        }
    }

    /**
     * Handles incoming broadcast events
     * @param {Object} data - Event data from the broadcast
     */
    async handleBroadcastEvent(data) {
        const { event, gameState } = data;

        switch (event) {
            case EVENT_TYPES.ROLLED_DICE:
                if (gameState?.last_roll !== undefined) {
                    this.resetEventSequence();
                    this.eventSequence.gameId = gameState.id;
                    
                    await this.executeHandlers('rolledDice', data, true);
                    this.eventSequence.rolledDice = true;
                    this.checkEventSequence();
                }
                break;

            case EVENT_TYPES.PLAYER_MOVED:
                await this.executeHandlers('playerMoved', data, true);
                this.eventSequence.playerMoved = true;
                this.checkEventSequence();
                break;

            case EVENT_TYPES.ENDED_TURN:
                await this.executeHandlers('endedTurn', data, false);
                this.eventSequence.endedTurn = true;
                this.checkEventSequence();
                break;

            default:
                console.warn('Unknown event type:', event);
        }
    }

    /**
     * Initializes the event manager and sets up WebSocket listeners
     */
    init() {
        const channel = window.Echo.channel('test-channel');
        
        channel.listen('BroadcastEvent', (data) => {
            this.handleBroadcastEvent(data);
        });

        // Register global game completion handler
        this.onAllEventsComplete(() => {
            // console.log('Game: All events complete, reloading page');
            window.location.reload(true);
        });
    }
}

// Create and expose the global instance
window.GameEventManager = new GameEventManager();

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.GameEventManager.init();
});
