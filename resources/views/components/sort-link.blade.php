<a href="{{ $url() }}" class="inline-flex items-center gap-1">
    {{ $label }}

    @if($isActive())
        @if($isAscending())
            {{-- Ascending --}}
            <svg class="w-4 h-4 text-gray-800" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6v13m0-13 4 4m-4-4-4 4" />
            </svg>
        @else
            {{-- Descending --}}
            <svg class="w-4 h-4 text-gray-800" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 19V5m0 14-4-4m4 4 4-4" />
            </svg>
        @endif
    @endif
</a>
