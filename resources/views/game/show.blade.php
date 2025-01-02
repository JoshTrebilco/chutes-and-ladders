<x-layout>
    <h1 class="text-4xl font-bold text-gray-800 dark:text-white">Chutes and Ladders</h1>
    <div class="mt-5 flex flex-col gap-5 lg:flex-row">
        <x-board :board="$board" />
        <x-panel :game="$game" :player_id="$player_id" />
    </div>
</x-layout>
