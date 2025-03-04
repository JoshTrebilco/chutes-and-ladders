<!-- Debug Button -->
<div class="fixed top-4 right-4 z-40">
    <button id="debug-toggle" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Debug
    </button>
</div>

<!-- Debug Slide-over Panel -->
<div id="debug-panel" class="fixed top-0 right-0 w-[600px] h-full bg-white shadow-lg transform translate-x-full transition-transform duration-300 ease-in-out z-50 overflow-y-auto">
    <div class="p-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Debug</h2>
            <button id="debug-close" class="text-gray-600 hover:text-gray-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div id="status" class="disconnected p-2 mb-2 rounded">Disconnected</div>
        <hr class="my-2">
        <div id="messages" class="mt-4"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const status = document.getElementById('status');
        const messages = document.getElementById('messages');
        const debugToggle = document.getElementById('debug-toggle');
        const debugPanel = document.getElementById('debug-panel');
        const debugClose = document.getElementById('debug-close');

        // Toggle debug panel
        debugToggle.addEventListener('click', function() {
            debugPanel.classList.toggle('translate-x-full');
        });

        // Close debug panel
        debugClose.addEventListener('click', function() {
            debugPanel.classList.add('translate-x-full');
        });

        function log(message) {
            console.log(message);
            const msg = document.createElement('div');
            msg.className = 'mb-2 p-2 border-b border-gray-200';

            // Add timestamp
            const timestamp = new Date().toLocaleTimeString();
            const timestampEl = document.createElement('span');
            timestampEl.className = 'text-xs text-gray-500';
            timestampEl.textContent = timestamp + ' ';
            msg.appendChild(timestampEl);

            // Handle different types of messages
            if (typeof message === 'object' && message !== null) {
                // If passed a direct object, format it
                formatJsonData(message, msg);
            } else if (typeof message === 'string' && message.startsWith('{')) {
                // If passed a JSON string, parse and format it
                try {
                    const json = JSON.parse(message);
                    formatJsonData(json, msg);
                } catch (e) {
                    // If JSON parsing fails, display the original message
                    msg.textContent += message;
                }
            } else {
                // For normal text messages
                msg.textContent += message;
            }

            messages.appendChild(msg);

            // Auto-scroll to bottom
            messages.scrollTop = messages.scrollHeight;
        }

        // Function to format JSON data into pretty UI elements
        function formatJsonData(json, container) {
            // Create container for formatted JSON
            const jsonContainer = document.createElement('div');
            jsonContainer.className = 'mt-1';

            // If it's an event, display the event name prominently
            if (json.event) {
                const eventName = document.createElement('div');
                eventName.className = 'font-bold text-blue-600 mb-1';
                eventName.textContent = 'Event: ' + json.event.split('\\').pop();
                jsonContainer.appendChild(eventName);
            }

            // Function to recursively render JSON
            function renderJson(obj, container, level = 0) {
                const indent = level * 12; // indent level in pixels

                if (Array.isArray(obj)) {
                    // Handle arrays
                    for (let i = 0; i < obj.length; i++) {
                        const itemRow = document.createElement('div');
                        itemRow.style.paddingLeft = `${indent}px`;
                        itemRow.className = 'flex items-start';

                        const keyEl = document.createElement('span');
                        keyEl.className = 'text-purple-600 mr-2';
                        keyEl.textContent = `[${i}]:`;
                        itemRow.appendChild(keyEl);

                        const valueContainer = document.createElement('div');
                        valueContainer.className = 'flex-1';

                        if (typeof obj[i] === 'object' && obj[i] !== null) {
                            keyEl.className += ' cursor-pointer';
                            keyEl.onclick = function() {
                                valueContainer.classList.toggle('hidden');
                            };
                            renderJson(obj[i], valueContainer, level + 1);
                        } else {
                            renderPrimitive(obj[i], valueContainer);
                        }

                        itemRow.appendChild(valueContainer);
                        container.appendChild(itemRow);
                    }
                } else if (typeof obj === 'object' && obj !== null) {
                    // Handle objects
                    for (const key in obj) {
                        const itemRow = document.createElement('div');
                        itemRow.style.paddingLeft = `${indent}px`;
                        itemRow.className = 'flex items-start';

                        const keyEl = document.createElement('span');
                        keyEl.className = 'text-blue-600 mr-2 font-medium';
                        keyEl.textContent = `${key}:`;
                        itemRow.appendChild(keyEl);

                        const valueContainer = document.createElement('div');
                        valueContainer.className = 'flex-1';

                        if (typeof obj[key] === 'object' && obj[key] !== null) {
                            keyEl.className += ' cursor-pointer';
                            keyEl.onclick = function() {
                                valueContainer.classList.toggle('hidden');
                            };
                            renderJson(obj[key], valueContainer, level + 1);
                        } else {
                            renderPrimitive(obj[key], valueContainer);
                        }

                        itemRow.appendChild(valueContainer);
                        container.appendChild(itemRow);
                    }
                }
            }

            // Function to render primitive values with appropriate styling
            function renderPrimitive(value, container) {
                const valueEl = document.createElement('span');

                if (typeof value === 'string') {
                    valueEl.className = 'text-green-600';
                    valueEl.textContent = `"${value}"`;
                } else if (typeof value === 'number') {
                    valueEl.className = 'text-orange-600';
                    valueEl.textContent = value;
                } else if (typeof value === 'boolean') {
                    valueEl.className = 'text-purple-600 font-medium';
                    valueEl.textContent = value;
                } else if (value === null) {
                    valueEl.className = 'text-gray-500 italic';
                    valueEl.textContent = 'null';
                }

                container.appendChild(valueEl);
            }

            renderJson(json, jsonContainer);
            container.appendChild(jsonContainer);
        }

        window.Echo.connector.pusher.connection.bind('connected', () => {
            status.textContent = 'Connected';
            status.className = 'connected p-2 mb-2 rounded bg-green-100 text-green-800';
            log('Connected to WebSocket server');
        });

        window.Echo.connector.pusher.connection.bind('disconnected', () => {
            status.textContent = 'Disconnected';
            status.className = 'disconnected p-2 mb-2 rounded bg-red-100 text-red-800';
            log('Disconnected from WebSocket server');
        });

        const channel = window.Echo.channel('test-channel');
        log('Subscribed to test-channel');

        channel.listen('BroadcastEvent', (data) => {
            // Directly log the data object without stringifying and adding prefix
            log(data);
        });
    });
</script>
