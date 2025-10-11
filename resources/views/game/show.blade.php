<x-layout>
    <!-- Header with Back Link -->
    <div class="mb-6">
        <a href="{{ route('games.index') }}"
            class="inline-flex items-center space-x-2 text-blue-300 hover:translate-x-[-2px] transition-transform">
            <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-2xl font-bold">Chutes and Ladders</span>
        </a>
    </div>

    <!-- Game Board -->
    <div class="mt-2 lg:mt-5 flex flex-col gap-6 lg:flex-row lg:items-start">
        <x-board :board="$board" :game="$game" :square-positions="$squarePositions" />
        <x-panel :game="$game" :auth_player="$authPlayer" />
        
        <!-- Winner Modal (controlled by JavaScript) -->
        <div id="winner-modal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm flex flex-col items-center justify-center hidden z-50">
            <div class="bg-slate-900/50 backdrop-blur-sm rounded-2xl p-8 border border-slate-800/50 shadow-xl text-center">
                <div class="flex items-center justify-center space-x-3 mb-6">
                    <div id="winner-token" class="w-10 h-10 rounded-full flex items-center justify-center">
                        <!-- Token will be populated by JavaScript -->
                    </div>
                    <h2 id="winner-text" class="text-2xl font-bold text-blue-300">
                        <!-- Winner text will be populated by JavaScript -->
                    </h2>
                </div>
                <a href="{{ route('games.index') }}"
                    class="inline-flex bg-gradient-to-r from-purple-600 to-blue-500 text-white rounded-lg px-6 py-3 font-semibold transform transition hover:translate-y-[-2px]">
                    Back to Games
                </a>
            </div>
        </div>
    </div>
</x-layout>
<script>
    class Game {
        constructor() {
            this.players = {!! json_encode($game->players()->map(fn($p) => ['id' => (string)$p->id, 'name' => $p->name, 'color' => $p->color])) !!};
            this.authPlayerId = '{{ $auth_player?->id ?? 'null' }}';
            this.activePlayerId = '{{ $game->activePlayer()?->id ?? 'null' }}';
            this.channel = window.Echo.channel('test-channel');
        }

        handleEvent(event, gameState) {
            if (event === 'App\\Events\\Gameplay\\PlayerWonGame' && gameState?.winner_id !== undefined) {
                this.showWinner(gameState.winner_id);
            }
        }

        showWinner(winnerId) {
            const winner = this.players.find(p => p.id === String(winnerId));
            if (!winner) return;

            const modal = document.getElementById('winner-modal');
            const token = document.getElementById('winner-token');
            const text = document.getElementById('winner-text');

            // Update token with winner's color and initial
            token.className = `w-10 h-10 rounded-full bg-${winner.color}-500 flex items-center justify-center`;
            token.innerHTML = `<span class="text-white font-bold text-lg">${winner.name.charAt(0).toUpperCase()}</span>`;

            // Update winner text
            text.textContent = `${winner.name} won the game!`;

            // Show modal
            modal.classList.remove('hidden');
        }

        init() {
            // Check if there's already a winner on page load
            @if($game->winner())
                this.showWinner('{{ $game->winner()->id }}');
            @endif

            this.channel.listen('BroadcastEvent', (data) => {
                this.handleEvent(data.event, data.gameState);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        window.game = new Game();
        window.game.init();
    });
</script>