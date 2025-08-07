@if ($paginator->hasPages())
    <nav aria-label="Page navigation" class="flex items-center justify-between px-4 py-2">
        <span class="text-sm font-normal text-gray-500">
            Showing
            <span class="font-semibold text-gray-900">{{ $paginator->firstItem() }}</span>
            to
            <span class="font-semibold text-gray-900">{{ $paginator->lastItem() }}</span>
            of
            <span class="font-semibold text-gray-900">{{ $paginator->total() }}</span>
        </span>

        <ul class="inline-flex -space-x-px text-sm h-8">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span class="flex items-center justify-center px-3 h-8 text-gray-400 bg-white border border-gray-300 rounded-s-lg cursor-not-allowed">Previous</span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" class="flex items-center justify-center px-3 h-8 text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700">Previous</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li>
                        <span class="flex items-center justify-center px-3 h-8 text-gray-500 bg-white border border-gray-300">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span class="flex items-center justify-center px-3 h-8 text-white bg-blue-600 border border-blue-600">{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}" class="flex items-center justify-center px-3 h-8 text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" class="flex items-center justify-center px-2 h-8 text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700">Next</a>
                </li>
            @else
                <li>
                    <span class="flex items-center justify-center px-2 h-8 text-gray-400 bg-white border border-gray-300 rounded-e-lg cursor-not-allowed">Next</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
