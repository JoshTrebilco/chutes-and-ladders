<svg style="width: {{ $size }}px; height: {{ $size }}px;" viewBox="0 0 {{ $size }} {{ $size }}">
    <g class="player-token">
        <!-- Token background -->
        <circle
            cx="{{ $size / 2 }}"
            cy="{{ $size / 2 }}"
            r="{{ $size * 0.3 }}"
            @class([
                'opacity-50 stroke-2 group-hover:opacity-80 transition-opacity',
                'fill-blue-500 stroke-blue-400' => $color === 'blue',
                'fill-green-500 stroke-green-400' => $color === 'green',
                'fill-red-500 stroke-red-400' => $color === 'red',
                'fill-yellow-500 stroke-yellow-400' => $color === 'yellow',
            ])
        />
        <!-- Token border -->
        <circle
            cx="{{ $size / 2 }}"
            cy="{{ $size / 2 }}"
            r="{{ $size * 0.3 }}"
            @class([
                'fill-none stroke-4',
                'stroke-blue-400' => $color === 'blue',
                'stroke-green-400' => $color === 'green',
                'stroke-red-400' => $color === 'red',
                'stroke-yellow-400' => $color === 'yellow',
            ])
        />
    </g>
</svg>
