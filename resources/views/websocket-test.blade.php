<!DOCTYPE html>
<html>
<head>
    <title>WebSocket Test (Reverb)</title>
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
        document.addEventListener("DOMContentLoaded", function () {
            const status = document.getElementById("status");
            const messages = document.getElementById("messages");

            function log(message) {
                console.log(message);
                const msg = document.createElement("div");
                msg.textContent = message;
                messages.appendChild(msg);
            }

            const connection = window.Echo.connector.pusher.connection;

            connection.bind("connected", () => {
                status.textContent = "Connected";
                status.className = "connected";
                log("✅ Connected to Reverb server");
            });

            connection.bind("disconnected", () => {
                status.textContent = "Disconnected";
                status.className = "disconnected";
                log("❌ Disconnected from Reverb server");
            });

            connection.bind("error", (error) => {
                log("⚠️ Connection error: " + JSON.stringify(error));
            });


            // Subscribe to a test channel
            const channel = window.Echo.channel("test");
            log("📡 Subscribed to channel: test");

            channel.listen("TestMessage", (data) => {
                log("🎉 Event received: " + JSON.stringify(data));
            });
        });
    </script>
</body>
</html>