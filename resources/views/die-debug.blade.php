<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Die Debug</title>
    @vite(['resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=play:400,500,600&display=swap" rel="stylesheet" />
    <script>tailwind.config={theme:{extend:{fontFamily:{'sans':['Play','ui-sans-serif','system-ui','sans-serif']}}}}}</script>
</head>
<body class="font-sans antialiased bg-slate-950 min-h-screen bg-gradient-to-b from-slate-950 to-purple-950 flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-2xl font-bold text-blue-300 mb-8">Debug Die</h1>
        <div id="die-container" class="flex justify-center"><x-die :value="null" /></div>
        <div class="mt-4 text-sm text-blue-200/60 text-center" id="status">Waiting for roll...</div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dieContainer = document.getElementById('die-container');
            const status = document.getElementById('status');
            const channel = window.Echo.channel('debug-channel');
            
            const dots = {
                1: '<span class="col-start-2 col-span-1 row-start-2 row-span-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>',
                2: '<span class="col-start-1 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-3 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>',
                3: '<span class="col-start-1 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-2 row-start-2 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-3 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>',
                4: '<span class="col-start-1 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-3 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-1 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-3 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>',
                5: '<span class="col-start-1 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-3 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-2 row-start-2 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-1 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-3 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>',
                6: '<span class="col-start-1 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-1 row-start-2 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-1 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-3 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-3 row-start-2 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-3 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>'
            };

            const createDie = v => `<div class="w-24 h-24 relative transform transition"><div class="absolute inset-0 bg-blue-500/20 rounded-2xl blur-lg"></div><div class="relative w-full h-full bg-slate-900/50 backdrop-blur-sm rounded-2xl border border-slate-800/50 shadow-xl grid grid-cols-3 gap-2 p-3">${dots[v]||''}</div></div>`;

            const rollAnimation = finalValue => {
                let i = 0;
                const timer = setInterval(() => {
                    dieContainer.innerHTML = createDie([4,2,1,6,3,5][i++ % 6]);
                }, 100);
                setTimeout(() => { clearInterval(timer); dieContainer.innerHTML = createDie(finalValue); }, 600);
            };

            channel.listen('BroadcastEvent', data => {
                if (data.gameState?.last_roll !== undefined) {
                    rollAnimation(data.gameState.last_roll);
                    status.textContent = `Last roll: ${data.gameState.last_roll}`;
                }
            });
        });
    </script>
</body>
</html>
