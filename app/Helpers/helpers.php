<?php

use Illuminate\Support\Facades\Request;

function sortableColumn(string $column, string $label): string
{
    $currentSort = Request::get('sort');
    $currentDirection = Request::get('direction', 'asc');
    $isActive = $currentSort === $column;

    $newDirection = $isActive && $currentDirection === 'asc' ? 'desc' : 'asc';

    $icon = '';
    if ($isActive) {
        $icon = $currentDirection === 'asc'
            ? '<i class="fas fa-sort-up ml-1"></i>'
            : '<i class="fas fa-sort-down ml-1"></i>';
    } else {
        $icon = '<i class="fas fa-sort ml-1 text-gray-400"></i>';
    }

    $query = http_build_query(array_merge(Request::except(['page', 'sort', 'direction']), [
        'sort' => $column,
        'direction' => $newDirection,
    ]));

    $url = Request::url() . '?' . $query;

    return "<a href=\"{$url}\" class=\"inline-flex items-center\">{$label} {$icon}</a>";
}

if (!function_exists('format_rupiah')) {
    function format_rupiah($amount, $with_fraction = false)
    {
        $decimals = $with_fraction ? 2 : 0;
        return 'Rp' . number_format($amount, $decimals, ',', '.');
    }
}