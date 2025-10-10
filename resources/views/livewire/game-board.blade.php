<div>
    <div class="mt-2 lg:mt-5 flex flex-col gap-6 lg:flex-row lg:items-start">
        <x-board :board="$board" :game="$game" />
        <x-panel :game="$game" :auth_player="$authPlayer" />
    </div>

    @if ($game->winner())
        <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm flex flex-col items-center justify-center">
            <div class="bg-slate-900/50 backdrop-blur-sm rounded-2xl p-8 border border-slate-800/50 shadow-xl text-center">
                <div class="flex items-center justify-center space-x-3 mb-6">
                    <x-token :size="40" :color="$game->winner()->color" />
                    <h2 class="text-2xl font-bold text-blue-300">{{ $game->winner()->name }} won the game!</h2>
                </div>
                <a href="{{ route('games.index') }}"
                    class="inline-flex bg-gradient-to-r from-purple-600 to-blue-500 text-white rounded-lg px-6 py-3 font-semibold transform transition hover:translate-y-[-2px]">
                    Back to Games
                </a>
            </div>
        </div>
    @endif
</div>
