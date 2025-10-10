<div class="relative w-[600px] h-[600px]">
    <div class="grid grid-cols-10 gap-1 w-full h-full bg-slate-900/50 backdrop-blur-sm p-4 rounded-2xl shadow-xl border border-slate-800/50">
        @foreach ($board->numbers as $number)
            <div class="aspect-square flex items-center justify-center border border-purple-500/20 rounded-lg
                {{ ($number % 2 == 0)
                    ? 'bg-slate-900/30 text-blue-300'
                    : 'bg-slate-800/30 text-blue-200' }}">
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

            <g class="stroke-purple-500" fill="none">
                <!-- Curved chute path -->
                <path d="M{{ $geometry['startX'] }} {{ $geometry['startY'] }}
                        C{{ $geometry['controlPoints']['c1x'] }} {{ $geometry['controlPoints']['c1y'] }}
                        {{ $geometry['controlPoints']['c2x'] }} {{ $geometry['controlPoints']['c2y'] }}
                        {{ $geometry['endX'] }} {{ $geometry['endY'] }}"
                        stroke-width="{{ $geometry['width'] }}"
                        class="opacity-10" />

                <!-- Chute edges -->
                <path d="M{{ $geometry['startX'] }} {{ $geometry['startY'] }}
                        C{{ $geometry['controlPoints']['c1x'] }} {{ $geometry['controlPoints']['c1y'] }}
                        {{ $geometry['controlPoints']['c2x'] }} {{ $geometry['controlPoints']['c2y'] }}
                        {{ $geometry['endX'] }} {{ $geometry['endY'] }}"
                        stroke-width="4"
                        class="opacity-40" />
            </g>
        @endforeach

        <!-- Draw Ladders -->
        @foreach ($board->ladders as $ladder)
            @php
                $geometry = $board->calculateLadderGeometry($ladder);
            @endphp

            <g class="stroke-blue-400 opacity-60" fill="none" stroke-width="4">
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
                <!-- Token glow effect -->
                <circle
                    cx="{{ $tokenX }}"
                    cy="{{ $tokenY }}"
                    r="18"
                    class="fill-{{ $player->color }}-500/20"
                />
                <!-- Token background -->
                <circle
                    cx="{{ $tokenX }}"
                    cy="{{ $tokenY }}"
                    r="15"
                    class="fill-{{ $player->color }}-500/50 stroke-{{ $player->color }}-400 stroke-2"
                />
                <!-- Token border -->
                <circle
                    cx="{{ $tokenX }}"
                    cy="{{ $tokenY }}"
                    r="15"
                    class="fill-none stroke-{{ $player->color }}-300 stroke-[3]"
                />
            </g>
        @endforeach
    </svg>
</div>

<script>
    // Board-specific event handling
    document.addEventListener('DOMContentLoaded', () => {
        // Register board-specific event handlers
        window.GameEventManager.onPlayerMoved(function(data) {
            console.log('Board: Player moved:', data);
            // TODO: Implement player movement animation on the board here
            return Promise.resolve();
        });

        window.GameEventManager.onAllEventsComplete(function() {
            console.log('Board: All events complete, resetting for next turn');
            // Board doesn't need to reload, just reset for next turn
            window.GameEventManager.resetEventSequence();
        });
    });
</script>