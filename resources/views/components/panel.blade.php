@props(['game', 'auth_player_id', 'channel'])
<div class="space-y-6">
    {{-- @if($game->hasPlayer($auth_player_id) && ! $game->activePlayer())
        <div class="rounded-md bg-green-50 p-4 mb-5">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">You are in this game!</p>
                </div>
            </div>
        </div>
    @endif --}}

    @if(! $game->hasPlayer($auth_player_id) && ! $game->isInProgress())
        <div class="bg-slate-900/50 backdrop-blur-sm rounded-2xl p-6 border border-slate-800/50 shadow-xl">
            <div class="flex items-center space-x-3 mb-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-purple-900/50 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 5a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm0 0v3m-4 1h8m-8 0c0 2 1.5 3 4 3s4-1 4-3m-8 0l-1 8h10l-1-8" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-blue-300">
                    Choose Your Token
                </h3>
            </div>
            <div class="flex justify-center gap-4">
                @foreach($game->available_colors as $color)
                    <form action="{{ route('players.join', ['game_id' => $game->id]) }}" method="post">
                        @csrf
                        <input type="hidden" name="color" value="{{ $color }}">
                        <button type="submit" class="transform transition hover:scale-110">
                            <x-token :color="$color" :size="50" />
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    @endif

    @if ($game->hasPlayer($auth_player_id) && !$game->isInProgress())
        <!-- Share Game Section -->
        <div class="bg-slate-900/50 backdrop-blur-sm rounded-2xl p-6 border border-slate-800/50 shadow-xl">
            <div class="flex items-center space-x-3 mb-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-purple-900/50 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-blue-300">
                    Invite Players
                </h3>
            </div>

            <div id="copy-section" class="space-y-3">
                <p class="text-blue-200/80 text-sm">Share this link with your friends to invite them to join your game:</p>

                <div class="flex gap-2">
                    <input
                        type="text"
                        readonly
                        value="{{ url('/games/' . $game->id) }}"
                        class="w-full px-4 py-2 rounded-lg border-2 border-purple-500/20 bg-slate-900/30 text-blue-200 text-sm focus:outline-none"
                    />
                    <button
                        id="copy-button"
                        class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-500 text-white rounded-lg font-semibold transform transition hover:translate-y-[-2px]"
                    >
                        <span id="copy-text">Copy</span>
                        <svg id="copy-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                        <svg id="check-icon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($game->created && $game->hasEnoughPlayers() && ! $game->isInProgress())
        <form action="{{ route('players.startGame', ['game_id' => $game->id]) }}" method="post">
            @csrf
            <button type="submit"
                class="w-full bg-gradient-to-r from-purple-600 to-blue-500 text-white rounded-lg px-4 py-3 font-semibold transform transition hover:translate-y-[-2px]">
                Start Game
            </button>
        </form>
    @endif

    @if(! $game->hasPlayer($auth_player_id) && $game->isInProgress())
        <div class="bg-slate-900/50 backdrop-blur-sm rounded-2xl p-6 border border-slate-800/50 shadow-xl lg:max-w-60">
                <div>
                    <h3 class="text-lg font-semibold text-blue-300">
                        Game in Progress
                    </h3>
                    <p class="text-blue-200/80 text-sm mt-4">
                        You're spectating this game.
                    </p>
                    <p class="text-blue-200/80 text-sm mt-2">
                        Wait for it to finish before joining a new one!
                    </p>
                </div>
        </div>
    @endif

    <div class="grid gap-6 grid-cols-2 lg:grid-cols-1">
        <!-- Players List -->
        <div class="bg-slate-900/50 backdrop-blur-sm rounded-2xl p-6 border border-slate-800/50 shadow-xl">
            <h3 class="text-lg font-semibold text-blue-300 mb-4">Players</h3>
            <ul class="space-y-3">
                @foreach ($game->players() as $player)
                    <li class="flex items-center space-x-3">
                        <x-token :color="$player->color" :size="25" />

                        <div class="flex items-center space-x-2 flex-grow">
                            <span class="text-blue-200">{{ $player->name }}</span>

                            <div class="flex items-center gap-2 ml-auto">
                                @if ($player->id == $auth_player_id)
                                    <span class="inline-flex items-center rounded-md bg-purple-400/10 px-2 py-1 text-xs font-medium text-purple-400 ring-1 ring-inset ring-purple-400/30">
                                        You
                                    </span>
                                @endif

                                @if ($player->id == $game->activePlayer()?->id)
                                    <svg class="w-4 h-4 text-blue-400 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Dice Roll Section -->
        <div class="bg-slate-900/50 backdrop-blur-sm rounded-2xl p-6 border border-slate-800/50 shadow-xl {{ $game->isInProgress() ? '' : 'hidden' }}">
            <div class="flex justify-center">
                <div class="flex flex-col items-center">
                    <div class="mb-2 h-24 w-24 flex items-center justify-center">
                        <div id="die-container" class="w-24 h-24">
                            <!-- Die will be rendered by JavaScript -->
                        </div>
                    </div>
                    <div class="mt-2 text-center text-blue-300 h-6 w-32 flex items-center justify-center" id="turn-text">
                        <!-- Turn text will be updated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    class Panel {
        constructor() {
            this.players = {!! json_encode($game->players()->map(fn($p) => ['id' => (string)$p->id, 'name' => $p->name])) !!};
            this.authPlayerId = '{{ $auth_player_id ?? 'null' }}';
            this.activePlayerId = '{{ $game->activePlayer()?->id ?? 'null' }}';
            this.dots = this.createDotsMap();
            this.channel = window.Echo.channel(@json($channel));
        }

        createDotsMap() {
            return {
                1: '<span class="col-start-2 col-span-1 row-start-2 row-span-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>',
                2: '<span class="col-start-1 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-3 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>',
                3: '<span class="col-start-1 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-2 row-start-2 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-3 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>',
                4: '<span class="col-start-1 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-3 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-1 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-3 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>',
                5: '<span class="col-start-1 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-3 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-2 row-start-2 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-1 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-3 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>',
                6: '<span class="col-start-1 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-1 row-start-2 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-1 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-3 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-3 row-start-2 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span><span class="col-start-3 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>'
            };
        }

        createDie(value) {
            return `<div class="w-24 h-24 relative transform transition">
                <div class="absolute inset-0 bg-blue-500/20 rounded-2xl blur-lg"></div>
                <div class="relative w-full h-full bg-slate-900/50 backdrop-blur-sm rounded-2xl border border-slate-800/50 shadow-xl grid grid-cols-3 gap-2 p-3">
                    ${this.dots[value] || ''}
                </div>
            </div>`;
        }

        async rollAnimation(containerId, finalValue) {
            const container = document.getElementById(containerId);
            if (!container) return;

            let i = 0;
            const timer = setInterval(() => {
                container.innerHTML = this.createDie([4,2,1,6,3,5][i++ % 6]);
            }, 100);

            setTimeout(() => { 
                clearInterval(timer); 
                container.innerHTML = this.createDie(finalValue); 
            }, 600);
        }

        updateUI() {
            const dieContainer = document.getElementById('die-container');
            const turnText = document.getElementById('turn-text');

            const isMyTurn = this.authPlayerId && this.activePlayerId === this.authPlayerId;

            this.toggleDiceButton(dieContainer, isMyTurn);
            this.updateTurnText(turnText, isMyTurn);
        }

        toggleDiceButton(dieContainer, isMyTurn) {
            const parentElement = dieContainer.parentElement;
            
            if (isMyTurn && parentElement.tagName !== 'BUTTON') {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'w-24 h-24 inline-flex rounded-2xl ring-2 ring-blue-500/50 shadow-[0_0_15px_rgba(59,130,246,0.5)] animate-[pulse_2s_ease-in-out_infinite]';
                button.onclick = () => this.rollDice();
                button.appendChild(dieContainer);
                parentElement.parentElement.replaceChild(button, parentElement);
            } else if (!isMyTurn && parentElement.tagName === 'BUTTON') {
                const div = document.createElement('div');
                div.className = 'w-24 h-24';
                div.appendChild(dieContainer);
                parentElement.parentElement.replaceChild(div, parentElement);
            }
        }

        updateTurnText(turnText, isMyTurn) {
            if (isMyTurn) {
                turnText.innerHTML = '<span class="inline-block animate-[pulse_2s_ease-in-out_infinite]">It\'s your turn</span>';
            } else {
                const activePlayer = this.players.find(p => p.id === this.activePlayerId);
                turnText.textContent = `It's ${activePlayer?.name || 'Unknown Player'}'s turn`;
            }
        }

        handleEvent(event, gameState) {
            if (event === 'App\\Events\\Gameplay\\RolledDice' && gameState?.last_roll !== undefined) {
                this.rollAnimation('die-container', gameState.last_roll);
            }
            
            if (event === 'App\\Events\\Gameplay\\EndedTurn' && gameState?.active_player_id !== undefined) {
                this.activePlayerId = String(gameState.active_player_id);
                this.updateUI();
            }
        }

        async rollDice() {
            this.rollAnimation('die-container', null);
            
            try {
                await axios.post('{{ $auth_player_id ? route('players.rollDice', ['game_id' => $game->id, 'player_id' => $auth_player_id]) : null }}', {
                    _token: '{{ csrf_token() }}'
                });
            } catch (error) {
                console.error('Error rolling dice:', error);
            }
        }

        setupCopyButton() {
            const copyButton = document.getElementById('copy-button');
            if (!copyButton) return;

            copyButton.addEventListener('click', async () => {
                try {
                    await navigator.clipboard.writeText('{{ url('/games/' . $game->id) }}');
                    this.showCopySuccess();
                } catch (err) {
                    console.error('Copy failed:', err);
                }
            });
        }

        showCopySuccess() {
            const copyText = document.getElementById('copy-text');
            const copyIcon = document.getElementById('copy-icon');
            const checkIcon = document.getElementById('check-icon');
            
            copyText.textContent = 'Copied!';
            copyIcon.classList.add('hidden');
            checkIcon.classList.remove('hidden');
            
            setTimeout(() => {
                copyText.textContent = 'Copy';
                copyIcon.classList.remove('hidden');
                checkIcon.classList.add('hidden');
            }, 2000);
        }

        init() {
            const currentRoll = {{ $game->last_roll ?? 'null' }};
            if (currentRoll) {
                document.getElementById('die-container').innerHTML = this.createDie(currentRoll);
            }

            this.channel.listen('BroadcastEvent', (data) => {
                this.handleEvent(data.event, data.gameState);
            });

            this.updateUI();
            this.setupCopyButton();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        window.panel = new Panel();
        window.panel.init();
    });

    function rollDice() {
        window.panel?.rollDice();
    }
</script>
