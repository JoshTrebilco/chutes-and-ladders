<x-layout>
    <a href="{{ route('games.index') }}" class="text-4xl font-bold text-gray-800 dark:text-white hover:text-gray-600 dark:hover:text-gray-300">Chutes and Ladders</a>
    <div class="mt-2 lg:mt-5 flex flex-col gap-5 lg:flex-row">
        <x-board :board="$board" :game="$game" />
        <x-panel :game="$game" :auth_player="$auth_player" />
    </div>
</x-layout>
