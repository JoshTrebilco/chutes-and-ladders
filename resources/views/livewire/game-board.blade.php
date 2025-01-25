<div wire:poll.1s="pollGameState">
    <div class="mt-2 lg:mt-5 flex flex-col gap-5 lg:flex-row">
        <x-board :board="$board" :game="$game" :auth_player="$authPlayer" />
        <x-panel :game="$game" :auth_player="$authPlayer" />
    </div>

    @if ($game->winner())
        <div class="fixed inset-0 bg-black/50 flex flex-col items-center justify-center">
            <div class="flex flex-col items-center justify-center bg-white dark:bg-gray-800 rounded-lg shadow-xl p-8 text-center text-2xl font-bold text-gray-800 dark:text-white">
                <div class="flex items-center justify-center">
                    <x-token :size="35" :color="$game->winner()->color" />
                    <div class="text-2xl font-bold">{{ $game->winner()->name }} won the game!</div>
                </div>
                <a href="{{ route('games.index') }}" class="mt-5 inline-flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 w-auto">Back to index</a>
            </div>
        </div>
    @endif
</div>
