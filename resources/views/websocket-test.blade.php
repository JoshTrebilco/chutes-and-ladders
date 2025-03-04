<!DOCTYPE html>
<html>
<head>
    <title>WebSocket Test</title>
    @vite(['resources/js/app.js'])
    <style>
        .connected { color: green; }
        .disconnected { color: red; }
    </style>
</head>
<body>
    <h1>WebSocket Test Page</h1>
    <div id="status" class="disconnected">Disconnected</div>
    <hr>
    <div id="messages"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const status = document.getElementById('status');
            const messages = document.getElementById('messages');

            function log(message) {
                console.log(message);
                const msg = document.createElement('div');
                msg.textContent = message;
                messages.appendChild(msg);
            }

            window.Echo.connector.pusher.connection.bind('connected', () => {
                status.textContent = 'Connected';
                status.className = 'connected';
                log('Connected to WebSocket server');
            });

            window.Echo.connector.pusher.connection.bind('disconnected', () => {
                status.textContent = 'Disconnected';
                status.className = 'disconnected';
                log('Disconnected from WebSocket server');
            });

            const channel = window.Echo.channel('test-channel');
            log('Subscribed to test-channel');

            channel.listen('BroadcastEvent', (data) => {
                log(`Event received: ${JSON.stringify(data)}`);
            });
        });
    </script>
</body>
</html>
