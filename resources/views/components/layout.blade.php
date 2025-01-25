<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Chutes and Ladders</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=play:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-slate-950">
        <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-b from-blue-100 to-purple-100 dark:from-slate-950 dark:to-purple-950 w-full">
            {{ $slot }}
        </div>
        @livewireScripts
    </body>
</html>
