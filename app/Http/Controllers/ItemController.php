<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Enums\UserRole;
use App\Models\StockHistory;

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
        
        // Inactive Status Toggler
        if ($request->has('is_inactive')) {
            $items->where('is_active', false);
        };

        // Low Stock Toggler
        if ($request-> has('low_stock')) {
            $items->whereBetween('quantity', [1, 10]);
        };

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
        // User can't add item
        $loggedUserRole = Auth::user()->role;
        if ($loggedUserRole === UserRole::USER) {
            return redirect()->route('items.index')->with('error', 'You do not have permission to add new item.');
        };

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
        // Find the item by ID with stockHistories and their users, ordered by created_at desc
        $item = Item::with([
            'stockHistories' => function ($q) {
                $q->with('user')->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);

        // Return the view for editing the item
        return view('items.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $loggedUser = Auth::user();
        $oldQty = $item->quantity;

        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        // If the delete_image flag is set, remove the image
        if ($request->input('delete_image')) {
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
                $item->image_path = null;
            }
        }

        // Delete the old image if a new one is uploaded
        if ($request->hasFile('image')) {
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }
            $item->image_path = $request->file('image')->store('images/items', 'public');
        }

        // Update the item
        $item->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'is_active' => $request->boolean('is_active'),
            'updated_at' => now(),
            'updated_by' => $loggedUser->id,
        ]);

        // Store to Stock History
        if ($oldQty != $request->quantity) {
            StockHistory::create([
                'item_id' => $item->id,
                'change' => $item->quantity - $oldQty,
                'old_quantity' => $oldQty,
                'new_quantity' => $item->quantity,
                'reason' => 'Stock Update',
                'user_id' => $loggedUser->id,
            ]);
        };

        // Log the update action
        Log::info("Item updated: {$item->name} by user ID: " . Auth::id());

        // Redirect to the items index with a success message
        return redirect()->route('items.index')->with('success', "{$item->name} updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Check if the user is authorized to delete the item
        $loggedUser = Auth::user();
        if ($loggedUser->role === UserRole::USER) {
            return redirect()->route('items.index')->with('error', 'You do not have permission to delete this item.');
        }

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
