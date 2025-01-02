<x-layout>
    <a href="{{ route('games.index') }}" class="text-4xl font-bold text-gray-800 dark:text-white hover:text-gray-600 dark:hover:text-gray-300">Chutes and Ladders</a>
    <livewire:game-board :game-id="$game->id" :auth-player-id="$auth_player_id" />
</x-layout>
