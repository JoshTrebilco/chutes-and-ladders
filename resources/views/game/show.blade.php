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
        <x-board :board="$board" :game="$game" />
        <x-panel :game="$game" :auth_player="$authPlayer" />
        @if($game->winner())
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
</x-layout>
