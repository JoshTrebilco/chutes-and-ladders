<x-layout>
    <h1 class="text-4xl font-bold text-gray-800 dark:text-white">Chutes and Ladders</h1>
    <div class="mt-5 flex flex-col gap-5 lg:flex-row">
        <x-board :board="$board" :game="$game" />
        <x-panel :game="$game" :auth_player="$auth_player" />
    </div>
</x-layout>
