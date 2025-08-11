<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        if (in_array($sort, ['name', 'price', 'quantity', 'is_active'])) {
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
        // Generate a new SKU for the item
        $sku = Item::generateSku();

        // Return the view for creating a new item
        return view('items.create', compact('sku'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:50|unique:items,sku',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        // Create a new item
        $loggedUser = Auth::user();

        $item = Item::create([
            'name' => $request->input('name'),
            'sku' => $request->input('sku'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'quantity' => $request->input('quantity'),
            'image_path' => $request->file('image') ? $request->file('image')->store('images/items', 'public') : null,
            'is_active' => $request->input('is_active', true),
            'created_by' => $loggedUser->id,
            'updated_by' => $loggedUser->id,
        ]);

        // Redirect to the items index with a success message
        return redirect()->route('items.index')->with('success', "{$item->name} created successfully.");
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
        // Find the item by ID
        $item = Item::findOrFail($id);

        // Return the view for editing the item
        return view('items.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:50|unique:items,sku,',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        // Find the item by ID
        $item = Item::findOrFail($id);

        // Update the item
        $item->update([
            'name' => $request->input('name'),
            'sku' => $request->input('sku'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'quantity' => $request->input('quantity'),
            'image_path' => $request->file('image') ? $request->file('image')->store('images/items', 'public') : $item->image_path,
            'is_active' => $request->input('is_active', true),
            'updated_by' => Auth::id(),
        ]);

        // Redirect to the items index with a success message
        return redirect()->route('items.index')->with('success', "{$item->name} updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the item by ID
        $item = Item::findOrFail($id);

        // Check if the item is already deleted
        if (!$item) {
            return redirect()->route('items.index')->with('error', 'Item not found.');
        }

        // Delete the image file if it exists
        if ($item->image_path) {
            if (file_exists(public_path('storage/' . $item->image_path))) {
                unlink(public_path('storage/' . $item->image_path));
            }
        }

        // Delete the item
        $item->delete();

        // Redirect to the items index with a success message
        return redirect()->route('items.index')->with('success', "{$item->name} deleted successfully.");
    }
}
