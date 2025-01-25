@props(['value' => null])

<div class="w-24 h-24 relative transform transition">
    <!-- Glow effect -->
    <div class="absolute inset-0 bg-blue-500/20 rounded-2xl blur-lg"></div>

    <!-- Die face -->
    <div class="relative w-full h-full bg-slate-900/50 backdrop-blur-sm rounded-2xl border border-slate-800/50 shadow-xl grid grid-cols-3 gap-2 p-3">
        @if($value == 1)
            <span class="col-start-2 col-span-1 row-start-2 row-span-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
        @endif

        @if($value == 2)
            <span class="col-start-1 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
            <span class="col-start-3 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
        @endif

        @if($value == 3)
            <span class="col-start-1 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
            <span class="col-start-2 row-start-2 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
            <span class="col-start-3 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
        @endif

        @if($value == 4)
            <span class="col-start-1 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
            <span class="col-start-3 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
            <span class="col-start-1 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
            <span class="col-start-3 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
        @endif

        @if($value == 5)
            <span class="col-start-1 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
            <span class="col-start-3 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
            <span class="col-start-2 row-start-2 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
            <span class="col-start-1 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
            <span class="col-start-3 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
        @endif

        @if($value == 6)
            <span class="col-start-1 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
            <span class="col-start-1 row-start-2 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
            <span class="col-start-1 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
            <span class="col-start-3 row-start-1 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
            <span class="col-start-3 row-start-2 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
            <span class="col-start-3 row-start-3 w-4 h-4 bg-blue-400 rounded-full shadow-lg shadow-blue-500/50"></span>
        @endif
    </div>
</div>
