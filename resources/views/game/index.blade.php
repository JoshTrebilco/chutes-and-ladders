<x-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h1 class="text-6xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-500 mb-4">
                Chutes and Ladders
            </h1>
            <p class="text-xl text-blue-300">
                Climb to victory, but watch out for those sneaky chutes! 🎲
            </p>
        </div>

        <!-- Game Options Cards -->
        <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <!-- Join Game Card -->
            <div class="bg-slate-950 rounded-2xl shadow-xl transform transition duration-500 hover:scale-105">
                <div class="p-8">
                    <div class="flex items-center justify-center w-16 h-16 bg-purple-900 rounded-full mb-4">
                        <svg class="w-8 h-8 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-blue-300 mb-4">Join a Game</h2>
                    <p class="text-blue-200 mb-6">Got an invite? Enter the game code below to join your friends!</p>

                    <div x-data="{ game_id: '' }">
                        <form @submit.prevent="if (game_id.trim() !== '') { window.location.href = `./games/${game_id}` }"
                                class="space-y-4">
                            <input
                                type="text"
                                x-model="game_id"
                                class="w-full px-4 py-3 rounded-lg border-2 border-purple-700 bg-slate-900 text-blue-100 placeholder-blue-500 focus:border-purple-500 focus:ring-0 focus:outline-none"
                                placeholder="Enter game code..."
                            />
                            <button
                                type="submit"
                                class="w-full bg-gradient-to-r from-purple-600 to-blue-500 text-white rounded-lg px-4 py-3 font-semibold transform transition hover:translate-y-[-2px]"
                                :disabled="game_id.trim() === ''"
                                :class="{ 'opacity-50 cursor-not-allowed': game_id.trim() === '' }"
                            >
                                Join Adventure
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Create Game Card -->
            <div class="bg-slate-950 rounded-2xl shadow-xl transform transition duration-500 hover:scale-105">
                <div class="p-8 h-full flex flex-col">
                    <div class="flex items-center justify-center w-16 h-16 bg-blue-900 rounded-full mb-4">
                        <svg class="w-8 h-8 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-blue-300 mb-4">Start New Game</h2>
                    <p class="text-blue-200 mb-6">Be the host! Create a new game and invite your friends to join the fun.</p>

                    <form action="{{ route('games.store') }}" method="post" class="mt-auto">
                        @csrf
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg px-4 py-3 font-semibold transform transition hover:translate-y-[-2px]">
                            Create New Adventure
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Auth Section -->
        <div class="mt-12 text-center">
            @if(session('user'))
                <div class="inline-flex items-center space-x-2 bg-slate-900 rounded-full px-6 py-3 shadow-lg">
                    <span class="text-blue-200">Playing as {{ session('user.name') }}</span>
                    <form action="{{ route('logout.destroy') }}" method="post" class="inline">
                        @csrf
                        <button type="submit"
                            class="text-red-500 hover:text-red-600 font-semibold">
                            Logout
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ route('login.index') }}"
                    class="inline-flex items-center space-x-2 bg-slate-900 rounded-full px-6 py-3 shadow-lg text-blue-200 hover:shadow-xl transition duration-300">
                    <span>Login to Save Progress</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @endif
        </div>

        <!-- Fun Footer -->
        <div class="mt-16 text-center text-blue-400">
            <p class="text-sm">🎲 Roll the dice and begin your adventure! 🎲</p>
        </div>
    </div>
</x-layout>
