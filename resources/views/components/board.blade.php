@props(['board', 'game', 'squarePositions', 'channel'])
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
            <g class="player-token" data-player-id="{{ $player->id }}" data-player-color="{{ $player->color }}">
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
    class Board {
        constructor() {
            this.squarePositions = @json($squarePositions);
            this.movementInProgress = false;
            this.channel = window.Echo.channel(@json($channel));
        }

        getToken(playerId) {
            const token = document.querySelector(`[data-player-id="${playerId}"]`);
            if (!token) {
                console.warn(`Token for player ${playerId} not found`);
            }
            return token;
        }

        updateTokenPosition(token, x, y, duration = '0.3s') {
            token.querySelectorAll('circle').forEach(circle => {
                circle.style.transition = `cx ${duration} ease-in-out, cy ${duration} ease-in-out`;
                circle.setAttribute('cx', x);
                circle.setAttribute('cy', y);
            });
        }

        async movePlayer(playerId, fromSquare, toSquare) {
            const token = this.getToken(playerId);
            if (!token) return;

            const from = parseInt(fromSquare) || 1;
            const to = parseInt(toSquare);
            
            if (from === to) return;
            
            const squares = this.generateMovementSquares(from, to);
            await this.animateThroughSquares(token, squares);
        }

        generateMovementSquares(from, to) {
            const squares = [];
            const step = from < to ? 1 : -1;
            for (let i = from + step; i !== to + step; i += step) {
                squares.push(i);
            }
            return squares;
        }

        async animateThroughSquares(token, squares) {
            for (const square of squares) {
                const [x, y] = this.squarePositions[square] || [0, 0];
                this.updateTokenPosition(token, x, y);
                await this.delay(300);
            }
        }

        async climbLadder(playerId, fromSquare, toSquare) {
            const token = this.getToken(playerId);
            if (!token) return;

            const [x, y] = this.squarePositions[toSquare] || [0, 0];
            this.updateTokenPosition(token, x, y, '0.8s');
            await this.delay(800);
        }

        async fallDownChute(playerId, fromSquare, toSquare) {
            const token = this.getToken(playerId);
            if (!token) return;

            const [x, y] = this.squarePositions[toSquare] || [0, 0];
            this.updateTokenPosition(token, x, y, '0.8s');
            await this.delay(800);
        }

        async waitForMovement() {
            return new Promise((resolve) => {
                const checkMovement = () => {
                    if (!this.movementInProgress) {
                        resolve();
                    } else {
                        setTimeout(checkMovement, 50);
                    }
                };
                checkMovement();
            });
        }

        delay(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }

        handleEvent(event, playerState) {
            if (event === 'App\\Events\\Gameplay\\PlayerMoved') {
                this.movementInProgress = true;
                this.movePlayer(playerState.id, playerState.previous_position, playerState.position)
                    .then(() => {
                        this.movementInProgress = false;
                    });
            }
            
            if (event === 'App\\Events\\Gameplay\\PlayerClimbedLadder') {
                this.waitForMovement().then(() => {
                    this.climbLadder(playerState.id, playerState.previous_position, playerState.position);
                });
            }
            
            if (event === 'App\\Events\\Gameplay\\PlayerFellDownChute') {
                this.waitForMovement().then(() => {
                    this.fallDownChute(playerState.id, playerState.previous_position, playerState.position);
                });
            }
        }

        init() {
            this.channel.listen('BroadcastEvent', (data) => {
                this.handleEvent(data.event, data.playerState);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        new Board().init();
    });
</script>