@props(['value' => null])

<div class="w-24 h-24 relative">
    <div class="w-full h-full bg-white rounded-xl border-2 border-gray-200 shadow-lg grid grid-cols-3 gap-2 p-3">
        @if($value == 1)
            <span class="col-start-2 col-span-1 row-start-2 row-span-1 w-4 h-4 bg-gray-900 rounded-full"></span>
        @endif

        @if($value == 2)
            <span class="col-start-1 row-start-1 w-4 h-4 bg-gray-900 rounded-full"></span>
            <span class="col-start-3 row-start-3 w-4 h-4 bg-gray-900 rounded-full"></span>
        @endif

        @if($value == 3)
            <span class="col-start-1 row-start-1 w-4 h-4 bg-gray-900 rounded-full"></span>
            <span class="col-start-2 row-start-2 w-4 h-4 bg-gray-900 rounded-full"></span>
            <span class="col-start-3 row-start-3 w-4 h-4 bg-gray-900 rounded-full"></span>
        @endif

        @if($value == 4)
            <span class="col-start-1 row-start-1 w-4 h-4 bg-gray-900 rounded-full"></span>
            <span class="col-start-3 row-start-1 w-4 h-4 bg-gray-900 rounded-full"></span>
            <span class="col-start-1 row-start-3 w-4 h-4 bg-gray-900 rounded-full"></span>
            <span class="col-start-3 row-start-3 w-4 h-4 bg-gray-900 rounded-full"></span>
        @endif

        @if($value == 5)
            <span class="col-start-1 row-start-1 w-4 h-4 bg-gray-900 rounded-full"></span>
            <span class="col-start-3 row-start-1 w-4 h-4 bg-gray-900 rounded-full"></span>
            <span class="col-start-2 row-start-2 w-4 h-4 bg-gray-900 rounded-full"></span>
            <span class="col-start-1 row-start-3 w-4 h-4 bg-gray-900 rounded-full"></span>
            <span class="col-start-3 row-start-3 w-4 h-4 bg-gray-900 rounded-full"></span>
        @endif

        @if($value == 6)
            <span class="col-start-1 row-start-1 w-4 h-4 bg-gray-900 rounded-full"></span>
            <span class="col-start-1 row-start-2 w-4 h-4 bg-gray-900 rounded-full"></span>
            <span class="col-start-1 row-start-3 w-4 h-4 bg-gray-900 rounded-full"></span>
            <span class="col-start-3 row-start-1 w-4 h-4 bg-gray-900 rounded-full"></span>
            <span class="col-start-3 row-start-2 w-4 h-4 bg-gray-900 rounded-full"></span>
            <span class="col-start-3 row-start-3 w-4 h-4 bg-gray-900 rounded-full"></span>
        @endif
    </div>
</div>
