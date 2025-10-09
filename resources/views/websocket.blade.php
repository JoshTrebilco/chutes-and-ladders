<!DOCTYPE html>
<html>
<head>
    <title>WebSocket Test (Reverb)</title>
    @vite(['resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Main Debug Interface -->
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">WebSocket Debug Console</h1>
                <div class="text-sm text-gray-600">
                    <p>Real-time WebSocket debugging with Laravel Reverb</p>
                </div>
            </div>
            
            <!-- Connection Status -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-4">Connection Status</h2>
                <div id="status" class="disconnected p-3 rounded-lg border">Disconnected</div>
            </div>
            
            <hr class="my-6">
            
            <!-- Message Log -->
            <div class="mb-4">
                <h2 class="text-xl font-semibold mb-4">Message Log</h2>
                <div id="messages" class="bg-gray-50 rounded-lg p-4 h-96 overflow-y-auto border"></div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const status = document.getElementById('status');
            const messages = document.getElementById('messages');

            function log(message) {
                console.log(message);
                const msg = document.createElement('div');
                msg.className = 'mb-2 border border-gray-200 rounded-lg overflow-hidden';

                // Create header with timestamp, event info, and toggle button
                const header = document.createElement('div');
                header.className = 'bg-gray-100 p-2 flex justify-between items-center cursor-pointer hover:bg-gray-200';
                
                const timestamp = new Date().toLocaleTimeString();
                const timestampEl = document.createElement('span');
                timestampEl.className = 'text-xs text-gray-500 font-mono mr-3';
                timestampEl.textContent = timestamp;
                
                // Extract event name and player info
                let eventInfo = '';
                if (typeof message === 'object' && message !== null) {
                    if (message.event) {
                        eventInfo = message.event.split('\\').pop();
                    }
                    if (message.playerState && message.playerState.name) {
                        eventInfo += eventInfo ? ` - ${message.playerState.name}` : message.playerState.name;
                    }
                } else if (typeof message === 'string' && message.startsWith('{')) {
                    try {
                        const json = JSON.parse(message);
                        if (json.event) {
                            eventInfo = json.event.split('\\').pop();
                        }
                        if (json.playerState && json.playerState.name) {
                            eventInfo += eventInfo ? ` - ${json.playerState.name}` : json.playerState.name;
                        }
                    } catch (e) {
                        // If JSON parsing fails, just show the message type
                        eventInfo = 'Text Message';
                    }
                } else {
                    eventInfo = 'Text Message';
                }
                
                const eventEl = document.createElement('span');
                eventEl.className = 'text-sm font-medium text-gray-700 flex-1';
                eventEl.textContent = eventInfo;
                
                const toggleIcon = document.createElement('span');
                toggleIcon.className = 'text-gray-500 text-sm ml-2';
                toggleIcon.textContent = '▶';
                
                header.appendChild(timestampEl);
                header.appendChild(eventEl);
                header.appendChild(toggleIcon);
                
                // Create content area (collapsed by default)
                const content = document.createElement('div');
                content.className = 'p-3 bg-white hidden';
                
                // Handle different types of messages
                if (typeof message === 'object' && message !== null) {
                    // If passed a direct object, format it
                    formatJsonData(message, content);
                } else if (typeof message === 'string' && message.startsWith('{')) {
                    // If passed a JSON string, parse and format it
                    try {
                        const json = JSON.parse(message);
                        formatJsonData(json, content);
                    } catch (e) {
                        // If JSON parsing fails, display the original message
                        content.textContent = message;
                    }
                } else {
                    // For normal text messages
                    content.textContent = message;
                }

                // Add toggle functionality
                header.addEventListener('click', function() {
                    const isCollapsed = content.classList.contains('hidden');
                    if (isCollapsed) {
                        content.classList.remove('hidden');
                        toggleIcon.textContent = '▼';
                    } else {
                        content.classList.add('hidden');
                        toggleIcon.textContent = '▶';
                    }
                });

                msg.appendChild(header);
                msg.appendChild(content);
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

            const connection = window.Echo.connector.pusher.connection;

            connection.bind('connected', () => {
                status.textContent = 'Connected';
                status.className = 'connected p-2 mb-2 rounded bg-green-100 text-green-800';
                log('✅ Connected to Reverb server');
            });

            connection.bind('disconnected', () => {
                status.textContent = 'Disconnected';
                status.className = 'disconnected p-2 mb-2 rounded bg-red-100 text-red-800';
                log('❌ Disconnected from Reverb server');
            });

            connection.bind('error', (error) => {
                log('⚠️ Connection error: ' + JSON.stringify(error));
            });

            const channelName = "test-channel";

            // Subscribe to a test channel
            const channel = window.Echo.channel(channelName);
            log("📡 Subscribed to channel: " + channelName);

            channel.listen("TestMessage", (data) => {
                log("TestMessage received: " + JSON.stringify(data));
            });

            channel.listen("BroadcastEvent", (data) => {
                // Directly log the data object without stringifying and adding prefix
                log(data);
            });
        });
    </script>
</body>
</html>