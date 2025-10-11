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
    const squarePositions = @json($squarePositions);

    const movePlayer = (playerId, fromSquare, toSquare) => {
        return new Promise((resolve) => {
            const token = document.querySelector(`[data-player-id="${playerId}"]`);
            if (!token) {
                console.warn(`Token for player ${playerId} not found`);
                return resolve();
            }
            
            const from = parseInt(fromSquare) || 1;
            const to = parseInt(toSquare);
            
            if (from === to) return resolve();
            
            // Generate squares to animate through
            const squares = [];
            const step = from < to ? 1 : -1;
            for (let i = from + step; i !== to + step; i += step) {
                squares.push(i);
            }
            
            // Animate through each square
            let index = 0;
            const animateNext = () => {
                if (index >= squares.length) return resolve();
                
                const square = squares[index++];
                const [x, y] = squarePositions[square] || [0, 0];
                
                // Update all circles in the token
                token.querySelectorAll('circle').forEach(circle => {
                    circle.style.transition = 'cx 0.3s ease-in-out, cy 0.3s ease-in-out';
                    circle.setAttribute('cx', x);
                    circle.setAttribute('cy', y);
                });
                
                setTimeout(animateNext, 300);
            };
            
            animateNext();
        });
    };

    const climbLadder = (playerId, fromSquare, toSquare) => {
        return new Promise((resolve) => {
            const token = document.querySelector(`[data-player-id="${playerId}"]`);
            if (!token) {
                console.warn(`Token for player ${playerId} not found`);
                return resolve();
            }
            
            const [x, y] = squarePositions[toSquare] || [0, 0];
            
            console.log(`Player ${playerId} climbing ladder from square ${fromSquare} to square ${toSquare}`);
            
            // Direct diagonal animation (no stepping)
            token.querySelectorAll('circle').forEach(circle => {
                circle.style.transition = 'cx 0.8s ease-in-out, cy 0.8s ease-in-out';
                circle.setAttribute('cx', x);
                circle.setAttribute('cy', y);
            });
            
            setTimeout(resolve, 800);
        });
    };

    const fallDownChute = (playerId, fromSquare, toSquare) => {
        return new Promise((resolve) => {
            const token = document.querySelector(`[data-player-id="${playerId}"]`);
            if (!token) {
                console.warn(`Token for player ${playerId} not found`);
                return resolve();
            }
            
            const [x, y] = squarePositions[toSquare] || [0, 0];
            
            console.log(`Player ${playerId} falling down chute from square ${fromSquare} to square ${toSquare}`);
            
            // Direct diagonal animation (no stepping)
            token.querySelectorAll('circle').forEach(circle => {
                circle.style.transition = 'cx 0.8s ease-in-out, cy 0.8s ease-in-out';
                circle.setAttribute('cx', x);
                circle.setAttribute('cy', y);
            });
            
            setTimeout(resolve, 800);
        });
    };

    // Board-specific event handling
    document.addEventListener('DOMContentLoaded', () => {
        let movementInProgress = false;
        
        const channel = window.Echo.channel('test-channel');
        
        channel.listen('BroadcastEvent', (data) => {
            const { event, playerState } = data;
            
            if (event === 'App\\Events\\Gameplay\\PlayerMoved') {
                movementInProgress = true;
                movePlayer(playerState.id, playerState.previous_position, playerState.position)
                    .then(() => {
                        movementInProgress = false;
                    });
            }
            
            if (event === 'App\\Events\\Gameplay\\PlayerClimbedLadder') {
                // Wait for movement to complete before starting ladder climb
                const waitForMovement = () => {
                    return new Promise((resolve) => {
                        const checkMovement = () => {
                            if (!movementInProgress) {
                                resolve();
                            } else {
                                setTimeout(checkMovement, 50);
                            }
                        };
                        checkMovement();
                    });
                };
                
                waitForMovement().then(() => {
                    climbLadder(playerState.id, playerState.previous_position, playerState.position);
                });
            }
            
            if (event === 'App\\Events\\Gameplay\\PlayerFellDownChute') {
                // Wait for movement to complete before starting chute fall
                const waitForMovement = () => {
                    return new Promise((resolve) => {
                        const checkMovement = () => {
                            if (!movementInProgress) {
                                resolve();
                            } else {
                                setTimeout(checkMovement, 50);
                            }
                        };
                        checkMovement();
                    });
                };
                
                waitForMovement().then(() => {
                    fallDownChute(playerState.id, playerState.previous_position, playerState.position);
                });
            }
        });
    });
</script>