<div class="relative w-[600px] h-[600px]">
    <div class="grid grid-cols-10 gap-1 w-full h-full bg-indigo-100 dark:bg-indigo-900 p-4 rounded-lg shadow-lg">
        @foreach ($board->numbers as $number)
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
        @foreach ($board->chutes as $chute)
            @php
                $geometry = $board->calculateChuteGeometry($chute);
            @endphp

            <g class="stroke-green-500 dark:stroke-green-400" fill="none">
                <!-- Curved chute path -->
                <path d="M{{ $geometry['startX'] }} {{ $geometry['startY'] }}
                        C{{ $geometry['controlPoints']['c1x'] }} {{ $geometry['controlPoints']['c1y'] }}
                        {{ $geometry['controlPoints']['c2x'] }} {{ $geometry['controlPoints']['c2y'] }}
                        {{ $geometry['endX'] }} {{ $geometry['endY'] }}"
                        stroke-width="{{ $geometry['width'] }}"
                        class="opacity-20 dark:opacity-20" />

                <!-- Chute edges -->
                <path d="M{{ $geometry['startX'] }} {{ $geometry['startY'] }}
                        C{{ $geometry['controlPoints']['c1x'] }} {{ $geometry['controlPoints']['c1y'] }}
                        {{ $geometry['controlPoints']['c2x'] }} {{ $geometry['controlPoints']['c2y'] }}
                        {{ $geometry['endX'] }} {{ $geometry['endY'] }}"
                        stroke-width="4"
                        class="opacity-60" />
            </g>
        @endforeach

        <!-- Draw Ladders -->
        @foreach ($board->ladders as $ladder)
            @php
                $geometry = $board->calculateLadderGeometry($ladder);
            @endphp

            <g class="stroke-amber-600 dark:stroke-amber-400 opacity-80 dark:opacity-60" fill="none" stroke-width="4">
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

        @foreach ($game->players() as $player)
            @php
                [$tokenX, $tokenY] = $board->getSquarePosition($player->position);
            @endphp
            <g class="player-token">
                <!-- Token background -->
                <circle
                    cx="{{ $tokenX }}"
                    cy="{{ $tokenY }}"
                    r="15"
                    class="fill-{{ $player->color }}-500 opacity-50 stroke-{{ $player->color }}-400 stroke-2"
                />
                <!-- Token border -->
                <circle
                    cx="{{ $tokenX }}"
                    cy="{{ $tokenY }}"
                    r="15"
                    class="fill-none stroke-{{ $player->color }}-400 stroke-4"
                />
            </g>
        @endforeach
    </svg>
</div>
