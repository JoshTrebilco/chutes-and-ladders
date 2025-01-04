@props(['game', 'auth_player'])
<div>
    {{-- @if($game->hasPlayer($auth_player?->id) && ! $game->activePlayer())
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

    @if(! $game->hasPlayer($auth_player?->id))
        <div class="rounded-md bg-yellow-50 p-4 mb-5">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 101.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-yellow-800">
                        Please select a color to join this game
                    </p>
                </div>
            </div>
            <div class="mt-4 flex justify-center">
                @foreach($game->available_colors as $color)
                    <form action="{{ route('players.join', ['game_id' => $game->id]) }}" method="post">
                        @csrf
                        <input type="hidden" name="color" value="{{ $color }}">
                        <button type="submit" class="group">
                            <x-token :color="$color" :size="50" />
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    @endif

    @if ($game->started && $game->hasEnoughPlayers() && ! $game->activePlayer())
        <form action="{{ route('players.startGame', ['game_id' => $game->id]) }}" method="post">
            @csrf
            <button type="submit" class="mb-5 inline-flex w-full items-center justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 lg:w-auto">
                Start Game
            </button>
        </form>
    @endif

    <div class="grid grid-cols-2 gap-5 lg:grid-cols-1 {{ $game->hasPlayer($auth_player?->id) ? '' : 'hidden' }}">
        <div class="overflow-hidden rounded-lg bg-white p-3 shadow lg:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">
                Players
            </dt>
            <dd class="mt-1 tracking-tight text-gray-900">
                <ul class="">
                    @foreach ($game->players() as $player)
                        <li class="text-sm text-nowrap flex items-center">
                            <x-token :color="$player->color" :size="20" />
                            {{ $player->name }}
                            @if ($player->id == $auth_player?->id)
                                (you)
                            @endif
                            @if ($player->id == $game->activePlayer()?->id)
                                (active)
                            @endif
                        </li>
                    @endforeach
                </ul>
            </dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white p-3 shadow lg:p-6 {{ $game->isInProgress() ? '' : 'hidden' }}">
            <dd class="text-gray-900">
                <div class="flex items-center justify-center p-2">
                    <x-die :value="$game->last_roll" />
                </div>
                @if ($game->hasPlayer($auth_player?->id) && $game->activePlayer()?->id == $auth_player?->id)
                    <form action="{{ route('players.rollDice', ['game_id' => $game->id, 'player_id' => $auth_player->id]) }}" method="post">
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                        >
                            Roll Dice
                        </button>
                    </form>
                @else
                    <div class="h-9 w-full">
                    </div>
                @endif
            </dd>
        </div>
    </div>
</div>
