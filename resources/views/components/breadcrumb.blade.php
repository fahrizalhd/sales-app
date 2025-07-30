@props(['menus' => []])

<nav class="text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
    <ol class="list-reset flex menus-center space-x-2 items-center">
        @foreach ($menus as $index => $menu)
            <li>
                @if (isset($menu['url']))
                    <a href="{{ $menu['url'] }}" class="hover:text-gray-700">{{ $menu['label'] }}</a>
                @else
                    <span class="text-gray-700">{{ $menu['label'] }}</span>
                @endif
            </li>

            @if ($index < count($menus) - 1)
                <li>
                    <svg class="w-3 h-3 text-gray-800" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M7.05 4.05a.5.5 0 01.7 0L13 9.29a1 1 0 010 1.42l-5.25 5.24a.5.5 0 01-.7-.7L11.79 10 7.05 5.34a.5.5 0 010-.7z"/>
                    </svg>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
