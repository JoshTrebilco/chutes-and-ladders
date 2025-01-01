<x-layout>
    <h1 class="text-4xl font-bold text-gray-800 dark:text-white">Chutes and Ladders</h1>

    <div class="mt-5 flex flex-col gap-5 lg:flex-row">

    <div class="relative w-[600px] h-[600px]">
        <div class="grid grid-cols-10 gap-1 w-full h-full bg-indigo-100 dark:bg-indigo-900 p-4 rounded-lg shadow-lg">
            @foreach ($board->numbers as $number)
                <div class="aspect-square flex items-center justify-center border border-indigo-300 dark:border-indigo-600 rounded
                    {{ ($number % 2 == 0)
                        ? 'bg-indigo-50 dark:bg-indigo-800 text-gray-700 dark:text-indigo-100'
                        : 'bg-white dark:bg-indigo-700 text-gray-700 dark:text-indigo-50' }}">
                    {{ $number }}
                </div>
            @endforeach
        </div>

        <svg class="absolute inset-0 pointer-events-none" viewBox="0 0 600 600">
            <!-- Draw Chutes first so they appear behind ladders -->
            @foreach ($board->chutes as $chute)
                @php
                    $geometry = $board->calculateChuteGeometry($chute);
                @endphp

                <g class="stroke-green-500 dark:stroke-green-400" fill="none">
                    <!-- Curved chute path -->
                    <path d="M{{ $geometry['startX'] }} {{ $geometry['startY'] }}
                            C{{ $geometry['controlPoints']['c1x'] }} {{ $geometry['controlPoints']['c1y'] }}
                            {{ $geometry['controlPoints']['c2x'] }} {{ $geometry['controlPoints']['c2y'] }}
                            {{ $geometry['endX'] }} {{ $geometry['endY'] }}"
                            stroke-width="{{ $geometry['width'] }}"
                            class="opacity-20 dark:opacity-20" />

                    <!-- Chute edges -->
                    <path d="M{{ $geometry['startX'] }} {{ $geometry['startY'] }}
                            C{{ $geometry['controlPoints']['c1x'] }} {{ $geometry['controlPoints']['c1y'] }}
                            {{ $geometry['controlPoints']['c2x'] }} {{ $geometry['controlPoints']['c2y'] }}
                            {{ $geometry['endX'] }} {{ $geometry['endY'] }}"
                            stroke-width="4"
                            class="opacity-60" />
                </g>
            @endforeach

            <!-- Draw Ladders -->
            @foreach ($board->ladders as $ladder)
                @php
                    $geometry = $board->calculateLadderGeometry($ladder);
                @endphp

                <g class="stroke-amber-600 dark:stroke-amber-400 opacity-80 dark:opacity-60" fill="none" stroke-width="4">
                    <!-- Main rails -->
                    <path d="M{{ $geometry['startX'] - $geometry['perpX'] }} {{ $geometry['startY'] - $geometry['perpY'] }}
                            L{{ $geometry['endX'] - $geometry['perpX'] }} {{ $geometry['endY'] - $geometry['perpY'] }}" />
                    <path d="M{{ $geometry['startX'] + $geometry['perpX'] }} {{ $geometry['startY'] + $geometry['perpY'] }}
                            L{{ $geometry['endX'] + $geometry['perpX'] }} {{ $geometry['endY'] + $geometry['perpY'] }}" />

                    <!-- Rungs -->
                    @for ($i = 1; $i < $geometry['steps']; $i++)
                        @php
                            $t = $i / $geometry['steps'];
                            $x = $geometry['startX'] + ($geometry['endX'] - $geometry['startX']) * $t;
                            $y = $geometry['startY'] + ($geometry['endY'] - $geometry['startY']) * $t;
                        @endphp
                        <path d="M{{ $x - $geometry['perpX'] }} {{ $y - $geometry['perpY'] }}
                                L{{ $x + $geometry['perpX'] }} {{ $y + $geometry['perpY'] }}" />
                    @endfor
                </g>
            @endforeach
        </svg>
    </div>

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
                            <li class="text-sm text-nowrap">{{ $player->name }} {{ $player->id == $player_id ? '(you)' : '' }}</li>
                        @endforeach
                    </ul>
                </dd>
            </div>
        </div>
    </div>

    </div>

</x-layout>
