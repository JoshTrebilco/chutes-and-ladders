<x-layout>
    <h1 class="text-4xl font-bold text-gray-800 dark:text-white">Chutes and Ladders</h1>
    <div class="bg-white shadow mt-6 sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900">
                Start a new game
            </h3>
            <div class="mt-2 max-w-xl text-sm text-gray-500">
                <p>First to get here? Start a new game and invite your friends!</p>
            </div>
            <form class="mt-5" action="{{ route('games.store') }}" method="post">
                @csrf
                <button
                    type="submit"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500"
                >
                    Start new game
                </button>
            </form>
        </div>
    </div>
</x-layout>
