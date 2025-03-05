@props(['value' => null])

<div x-data="{
    value: {{ $value ?: 'null' }},
    rolling: false,
    rollInterval: null,

    startRoll() {
        this.rolling = true;

        // Simple random roll animation
        this.rollInterval = setInterval(() => {
            this.value = Math.floor(Math.random() * 6) + 1;
        }, 150);
    },

    stopRoll(finalValue) {
        // Slow down first
        clearInterval(this.rollInterval);
        this.rollInterval = setInterval(() => {
            this.value = Math.floor(Math.random() * 6) + 1;
        }, 300);

        // Then stop on correct value
        setTimeout(() => {
            clearInterval(this.rollInterval);
            this.value = finalValue;
            this.rolling = false;
        }, 600);
    }
}"
class="w-24 h-24 relative transform transition"
x-bind:class="{ 'animate-pulse': rolling }">
    <!-- Glow effect -->
    <div class="absolute inset-0 bg-blue-500/20 rounded-2xl blur-lg"></div>

    <!-- Die face -->
    <div class="relative w-full h-full bg-slate-900/50 backdrop-blur-sm rounded-2xl border border-slate-800/50 shadow-xl grid grid-cols-3 gap-2 p-3">
        <template x-if="value == 1">
            <span class="col-start-2 col-span-1 row-start-2 row-span-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
        </template>

        <template x-if="value == 2">
            <div class="contents">
                <span class="col-start-1 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
                <span class="col-start-3 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
            </div>
        </template>

        <template x-if="value == 3">
            <div class="contents">
                <span class="col-start-1 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
                <span class="col-start-2 row-start-2 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
                <span class="col-start-3 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
            </div>
        </template>

        <template x-if="value == 4">
            <div class="contents">
                <span class="col-start-1 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
                <span class="col-start-3 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
                <span class="col-start-1 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
                <span class="col-start-3 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
            </div>
        </template>

        <template x-if="value == 5">
            <div class="contents">
                <span class="col-start-1 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
                <span class="col-start-3 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
                <span class="col-start-2 row-start-2 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
                <span class="col-start-1 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
                <span class="col-start-3 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
            </div>
        </template>

        <template x-if="value == 6">
            <div class="contents">
                <span class="col-start-1 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
                <span class="col-start-1 row-start-2 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
                <span class="col-start-1 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
                <span class="col-start-3 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
                <span class="col-start-3 row-start-2 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
                <span class="col-start-3 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
            </div>
        </template>
    </div>
</div>
