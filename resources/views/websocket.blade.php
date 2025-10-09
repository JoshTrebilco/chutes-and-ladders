<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebSocket Debug Console</title>
    @vite(['resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'mono': ['JetBrains Mono', 'Fira Code', 'Monaco', 'Consolas', 'monospace'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-slate-900">WebSocket Debug Console</h1>
                        <p class="text-sm text-slate-500">Real-time event monitoring with Laravel Reverb</p>
                    </div>
                </div>
                <div class="flex items-center space-x-6">
                    <!-- Stats in header -->
                    <div class="flex items-center space-x-4">
                        <div class="text-center">
                            <div class="text-xs text-slate-500 uppercase tracking-wide font-medium">Events</div>
                            <div class="text-lg font-bold text-slate-900" id="event-count">0</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xs text-slate-500 uppercase tracking-wide font-medium">Players</div>
                            <div class="text-lg font-bold text-slate-900" id="player-count">0</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xs text-slate-500 uppercase tracking-wide font-medium">Last Event</div>
                            <div class="text-sm font-medium text-slate-900" id="last-event-time">Never</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-slate-500 uppercase tracking-wide font-medium">Status</div>
                        <div id="status" class="disconnected text-sm font-medium">Disconnected</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Message Log -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 h-[calc(100vh-8rem)] flex flex-col">
            <div class="px-6 py-4 border-b border-slate-200 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Event Log</h2>
                    <div class="flex items-center space-x-2">
                        <button id="clear-log" class="text-sm text-slate-500 hover:text-slate-700 px-3 py-1 rounded-md hover:bg-slate-100 transition-colors">
                            Clear Log
                        </button>
                        <button id="expand-all" class="text-sm text-slate-500 hover:text-slate-700 px-3 py-1 rounded-md hover:bg-slate-100 transition-colors">
                            Expand All
                        </button>
                        <button id="collapse-all" class="text-sm text-slate-500 hover:text-slate-700 px-3 py-1 rounded-md hover:bg-slate-100 transition-colors">
                            Collapse All
                        </button>
                    </div>
                </div>
            </div>
            <div id="messages" class="flex-1 overflow-y-auto bg-slate-50/50"></div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const status = document.getElementById('status');
            const messages = document.getElementById('messages');
            const eventCount = document.getElementById('event-count');
            const lastEventTime = document.getElementById('last-event-time');
            const playerCount = document.getElementById('player-count');
            const clearLogBtn = document.getElementById('clear-log');
            const expandAllBtn = document.getElementById('expand-all');
            const collapseAllBtn = document.getElementById('collapse-all');

            let eventCounter = 0;
            let uniquePlayers = new Set();

            // Button event listeners
            clearLogBtn.addEventListener('click', function() {
                messages.innerHTML = '';
                eventCounter = 0;
                uniquePlayers.clear();
                updateStats();
            });

            expandAllBtn.addEventListener('click', function() {
                const contents = messages.querySelectorAll('.event-content');
                const icons = messages.querySelectorAll('.toggle-icon');
                contents.forEach(content => content.classList.remove('hidden'));
                icons.forEach(icon => icon.textContent = '▼');
            });

            collapseAllBtn.addEventListener('click', function() {
                const contents = messages.querySelectorAll('.event-content');
                const icons = messages.querySelectorAll('.toggle-icon');
                contents.forEach(content => content.classList.add('hidden'));
                icons.forEach(icon => icon.textContent = '▶');
            });

            function updateStats() {
                eventCount.textContent = eventCounter;
                playerCount.textContent = uniquePlayers.size;
            }

            function log(message) {
                console.log(message);
                eventCounter++;
                
                const msg = document.createElement('div');
                msg.className = 'border-b border-slate-200 last:border-b-0';

                // Create header with timestamp, event info, and toggle button
                const header = document.createElement('div');
                header.className = 'px-6 py-4 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition-colors group';
                
                const timestamp = new Date().toLocaleTimeString();
                const timestampEl = document.createElement('span');
                timestampEl.className = 'text-xs text-slate-500 font-mono mr-4';
                timestampEl.textContent = timestamp;
                
                // Extract event name and player info
                let eventInfo = '';
                let playerName = '';
                if (typeof message === 'object' && message !== null) {
                    if (message.event) {
                        eventInfo = message.event.split('\\').pop();
                    }
                    if (message.playerState && message.playerState.name) {
                        playerName = message.playerState.name;
                        uniquePlayers.add(playerName);
                    }
                } else if (typeof message === 'string' && message.startsWith('{')) {
                    try {
                        const json = JSON.parse(message);
                        if (json.event) {
                            eventInfo = json.event.split('\\').pop();
                        }
                        if (json.playerState && json.playerState.name) {
                            playerName = json.playerState.name;
                            uniquePlayers.add(playerName);
                        }
                    } catch (e) {
                        eventInfo = 'Text Message';
                    }
                } else {
                    eventInfo = 'Text Message';
                }
                
                const eventEl = document.createElement('div');
                eventEl.className = 'flex-1 flex items-center space-x-3';
                
                const eventName = document.createElement('span');
                eventName.className = 'text-sm font-semibold text-slate-900';
                eventName.textContent = eventInfo;
                
                if (playerName) {
                    const playerEl = document.createElement('span');
                    playerEl.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800';
                    playerEl.textContent = playerName;
                    eventEl.appendChild(eventName);
                    eventEl.appendChild(playerEl);
                } else {
                    eventEl.appendChild(eventName);
                }
                
                const toggleIcon = document.createElement('span');
                toggleIcon.className = 'toggle-icon text-slate-400 text-sm group-hover:text-slate-600 transition-colors';
                toggleIcon.textContent = '▶';
                
                header.appendChild(timestampEl);
                header.appendChild(eventEl);
                header.appendChild(toggleIcon);
                
                // Create content area (collapsed by default)
                const content = document.createElement('div');
                content.className = 'event-content px-6 pb-4 bg-slate-50/30 hidden';
                
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

                // Update stats
                updateStats();
                lastEventTime.textContent = new Date().toLocaleTimeString();

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
                status.className = 'connected text-sm font-medium text-green-600';
                log('✅ Connected to Reverb server');
            });

            connection.bind('disconnected', () => {
                status.textContent = 'Disconnected';
                status.className = 'disconnected text-sm font-medium text-red-600';
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