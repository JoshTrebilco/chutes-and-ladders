@props(['game', 'player_id'])
<div>
    @if($player_id && $game->hasPlayer($player_id))
        <div class="rounded-md bg-green-50 p-4">
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
    @else
        <form action="{{ route('players.store', ['game_id' => $game->id]) }}" method="post">
            @csrf
            <button
                type="submit"
                class="inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 lg:w-auto"
            >
                Join this game
            </button>
        </form>
    @endif

    <div class="mt-5 grid grid-cols-2 gap-5 lg:grid-cols-1">
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow lg:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">
                Total players
            </dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                {{ count($game->player_ids) }}
            </dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow lg:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">
                Players
            </dt>
            <dd class="mt-1 tracking-tight text-gray-900">
                <ul class="">
                    @foreach ($game->players() as $player)
                        <li class="text-sm text-nowrap">{{ $player->name }} {{ $player->id == $player_id ? '(you)' : '' }} {{ $player->id == $game->activePlayer()?->id ? '(active)' : '' }}</li>
                    @endforeach
                </ul>
            </dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow lg:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">
                Last roll
            </dt>
            <dd class="text-gray-900">
                <div class="flex items-center justify-center p-4">
                    <x-die :value="$game->last_roll" />
                </div>
                <div class="text-3xl font-semibold tracking-tight text-center">
                    {{ $game->last_roll ?: '-' }}
                </div>
            </dd>
        </div>
        @if ($player_id && $game->hasPlayer($player_id) && $game->activePlayer()?->id == $player_id)
            <form action="{{ route('players.rollDice', ['game_id' => $game->id, 'player_id' => $player_id]) }}" method="post">
                @csrf
                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 lg:w-auto"
                >
                    Roll Dice
                </button>
            </form>
        @endif
        @if ($game->started && $game->hasEnoughPlayers() && ! $game->activePlayer())
            <form action="{{ route('players.startGame', ['game_id' => $game->id]) }}" method="post">
                @csrf
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 lg:w-auto">
                    Start Game
                </button>
            </form>
        @endif
    </div>
</div>
