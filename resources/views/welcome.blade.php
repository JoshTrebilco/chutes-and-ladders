<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Shoots and Ladders</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=play:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-black">
        <div class="min-h-screen flex flex-col items-center justify-center">
            <h1 class="text-4xl font-bold text-gray-800 dark:text-white">Shoots and Ladders</h1>
            <div class="mt-6 grid grid-cols-10 gap-1 w-[600px] h-[600px] bg-indigo-100 dark:bg-indigo-900 p-4 rounded-lg shadow-lg">
                @php
                    $numbers = [];
                    for ($row = 9; $row >= 0; $row--) {
                        $start = ($row * 10) + 1;
                        $rowNumbers = range($start, $start + 9);
                        if ($row % 2 !== 0) {
                            $rowNumbers = array_reverse($rowNumbers);
                        }
                        $numbers = array_merge($numbers, $rowNumbers);
                    }
                @endphp

                @foreach ($numbers as $number)
                    <div class="aspect-square flex items-center justify-center border border-indigo-300 dark:border-indigo-600 rounded
                        {{ ($number % 2 == 0)
                            ? 'bg-indigo-50 dark:bg-indigo-800 text-gray-700 dark:text-indigo-100'
                            : 'bg-white dark:bg-indigo-700 text-gray-700 dark:text-indigo-50' }}">
                        {{ $number }}
                    </div>
                @endforeach
            </div>
        </div>
    </body>
</html>
