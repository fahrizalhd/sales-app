<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SortLink extends Component
{
    public string $column;
    public string $label;

    public function __construct(string $column, string $label)
    {
        $this->column = $column;
        $this->label = $label;
    }

    public function render()
    {
        return view('components.sort-link');
    }

    public function nextDirection(): string
    {
        $currentSort = request('sort');
        $currentDirection = request('direction', 'asc');

        if ($currentSort === $this->column || $currentSort === "-{$this->column}") {
            return $currentDirection === 'asc' ? 'desc' : 'asc';
        }

        return 'asc';
    }

    public function isActive(): bool
    {
        return request('sort') === $this->column || request('sort') === "-{$this->column}";
    }

    public function isAscending(): bool
    {
        return request('sort') === $this->column;
    }

    public function url(): string
    {
        $direction = $this->nextDirection();
        $sortValue = $direction === 'desc' ? "-{$this->column}" : $this->column;

        return request()->fullUrlWithQuery(array_merge(
            request()->only(['search']),
            [
            'sort' => $sortValue,
            'direction' => $direction,
            ]
            ));
    }
}
