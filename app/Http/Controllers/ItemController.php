<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the search term from the request
        $search = $request->input('search');

        // Query the items with optional search functionality
        $items = Item::when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('sku', 'like', '%' . $search . '%');
        });

        // Filter by active status if specified
        if ($request->has('status')) {
            $isActive = $request->input('status');
            if ($isActive === '1') {
                $items->where('is_active', true);
            } elseif ($isActive === '0') {
                $items->where('is_active', false);
            }
        }

        // Sorting logic
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');

        // Validate sort and direction
        if (in_array($sort, ['name', 'sku', 'price', 'quantity'])) {
            $items->orderBy($sort, $direction);
        }

        // Paginate the results
        $items = $items->paginate(10)->withQueryString();

        // Return a view with the items
        return view('items.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
