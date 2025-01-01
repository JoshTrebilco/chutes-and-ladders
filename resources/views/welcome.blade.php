<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Chutes and Ladders</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=play:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-black">
        <div class="min-h-screen flex flex-col items-center justify-center">
            <h1 class="text-4xl font-bold text-gray-800 dark:text-white">Chutes and Ladders</h1>
            <div class="mt-6 relative w-[600px] h-[600px]">
                <div class="grid grid-cols-10 gap-1 w-full h-full bg-indigo-100 dark:bg-indigo-900 p-4 rounded-lg shadow-lg">
                    @foreach ($numbers as $number)
                        <div class="aspect-square flex items-center justify-center border border-indigo-300 dark:border-indigo-600 rounded
                            {{ ($number % 2 == 0)
                                ? 'bg-indigo-50 dark:bg-indigo-800 text-gray-700 dark:text-indigo-100'
                                : 'bg-white dark:bg-indigo-700 text-gray-700 dark:text-indigo-50' }}">
                            {{ $number }}
                        </div>
                    @endforeach
                </div>

                <svg class="absolute inset-0 pointer-events-none" viewBox="0 0 600 600">
                    <!-- Draw Chutes first so they appear behind ladders -->
                    @foreach ($chutes as $chute)
                        @php
                            $geometry = app(App\Http\Controllers\GameController::class)
                                ->calculateChuteGeometry($chute);
                        @endphp

                        <g class="stroke-red-500 dark:stroke-red-400" fill="none">
                            <!-- Curved chute path -->
                            <path d="M{{ $geometry['startX'] }} {{ $geometry['startY'] }}
                                   C{{ $geometry['controlPoints']['c1x'] }} {{ $geometry['controlPoints']['c1y'] }}
                                    {{ $geometry['controlPoints']['c2x'] }} {{ $geometry['controlPoints']['c2y'] }}
                                    {{ $geometry['endX'] }} {{ $geometry['endY'] }}"
                                  stroke-width="{{ $geometry['width'] }}"
                                  class="opacity-20 dark:opacity-10" />

                            <!-- Chute edges -->
                            <path d="M{{ $geometry['startX'] }} {{ $geometry['startY'] }}
                                   C{{ $geometry['controlPoints']['c1x'] }} {{ $geometry['controlPoints']['c1y'] }}
                                    {{ $geometry['controlPoints']['c2x'] }} {{ $geometry['controlPoints']['c2y'] }}
                                    {{ $geometry['endX'] }} {{ $geometry['endY'] }}"
                                  stroke-width="2"
                                  class="opacity-100" />
                        </g>
                    @endforeach

                    <!-- Draw Ladders -->
                    @foreach ($ladders as $ladder)
                        @php
                            $geometry = app(App\Http\Controllers\GameController::class)
                                ->calculateLadderGeometry($ladder);
                        @endphp

                        <g class="stroke-amber-600 dark:stroke-amber-400" fill="none" stroke-width="4">
                            <!-- Main rails -->
                            <path d="M{{ $geometry['startX'] - $geometry['perpX'] }} {{ $geometry['startY'] - $geometry['perpY'] }}
                                   L{{ $geometry['endX'] - $geometry['perpX'] }} {{ $geometry['endY'] - $geometry['perpY'] }}" />
                            <path d="M{{ $geometry['startX'] + $geometry['perpX'] }} {{ $geometry['startY'] + $geometry['perpY'] }}
                                   L{{ $geometry['endX'] + $geometry['perpX'] }} {{ $geometry['endY'] + $geometry['perpY'] }}" />

                            <!-- Rungs -->
                            @for ($i = 1; $i < $geometry['steps']; $i++)
                                @php
                                    $t = $i / $geometry['steps'];
                                    $x = $geometry['startX'] + ($geometry['endX'] - $geometry['startX']) * $t;
                                    $y = $geometry['startY'] + ($geometry['endY'] - $geometry['startY']) * $t;
                                @endphp
                                <path d="M{{ $x - $geometry['perpX'] }} {{ $y - $geometry['perpY'] }}
                                       L{{ $x + $geometry['perpX'] }} {{ $y + $geometry['perpY'] }}" />
                            @endfor
                        </g>
                    @endforeach
                </svg>
            </div>
        </div>
    </body>
</html>
