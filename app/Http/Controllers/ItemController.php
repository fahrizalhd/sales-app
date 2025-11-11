<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Enums\UserRole;
use App\Models\StockHistory;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ItemController extends Controller
{
    /**
     * Display a listing of the items with filtering and sorting.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $lowStockItemThresholdMin = config('filters.item.low_stock_threshold_min');
        $lowStockItemThresholdMax = config('filters.item.low_stock_threshold_max');

        $items = QueryBuilder::for(Item::class)
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $query
                        ->where('name', 'like', "%{$value}%")
                        ->orWhere('sku', 'like', "%{$value}%")
                        ->orWhereHas('category', function ($q) use ($value) {
                            $q->where('name', 'like', "%{$value}%");
                        });
                }),
                // For Quick Filter
                AllowedFilter::exact('is_active'),
                AllowedFilter::callback('low_stock', function ($query, $value) use ($lowStockItemThresholdMin, $lowStockItemThresholdMax) {
                    if ($value) {
                        $query->whereBetween('quantity', [$lowStockItemThresholdMin, $lowStockItemThresholdMax]);
                    }
                }),
            ])
            ->allowedSorts([
                'name',
                'price',
                'quantity',
                'is_active',
                'updated_at',
                AllowedSort::callback('category', function ($query, $descending) {
                    $query
                        ->join('categories', 'items.category_id', '=', 'categories.id')
                        ->orderBy('categories.name', $descending ? 'desc' : 'asc')
                        ->select('items.*');
                }),
            ])
            ->with('category')
            ->paginate(10)
            ->withQueryString();

        return view('items.index', compact('items'));
    }

    /**
     * Show the form for creating a new item.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        // User can't add item
        $loggedUserRole = Auth::user()->role;
        if ($loggedUserRole === UserRole::USER) {
            return redirect()
                ->route('items.index')
                ->with('error', 'You do not have permission to add new item.');
        }

        // Generate a new SKU for the item
        $sku = Item::generateSku();

        // Generate Categories Dropdown
        $categories = Category::orderBy('name')->get();

        // Return the view for creating a new item
        return view('items.create', compact('sku', 'categories'));
    }

    /**
     * Store a newly created item in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name'          => 'required|string|max:255',
            'sku'           => 'nullable|string|max:50|unique:items,sku',
            'description'   => 'nullable|string|max:1000',
            'price'         => 'required|numeric|min:0',
            'cost'          => 'required|numeric|min:0',
            'quantity'      => 'required|integer|min:0',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active'     => 'boolean',
            'category_id'   => 'required|exists:categories,id',
        ]);

        // Create a new item
        $item = Item::create([
            'name'          => $request->input('name'),
            'sku'           => $request->input('sku'),
            'description'   => $request->input('description'),
            'price'         => $request->input('price'),
            'cost'          => $request->input('cost'),
            'quantity'      => $request->input('quantity'),
            'image_path'    => $request->file('image')
                                ? $request->file('image')->store('images/items', 'public')
                                : null,
            'is_active'     => $request->input('is_active', true),
            'category_id'   => $request->input('category_id'),
        ]);

        // Redirect to the items index with a success message
        return redirect()
            ->route('items.index')
            ->with('success', "{$item->name} created successfully.");
    }

    /**
     * Display the specified item.
     *
     * @param  string  $id
     * @return void
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified item.
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function edit(string $id)
    {
        // Find the item by ID with stockHistories and their users, ordered by created_at desc
        $item = Item::with([
            'stockHistories' => function ($q) {
                $q->with('createdBy', 'updatedBy')->orderBy('created_at', 'desc');
            },
        ])->findOrFail($id);

        // Generate Categories Dropdown
        $categories = Category::orderBy('name')->get();

        // Return the view for editing the item
        return view('items.edit', compact('item', 'categories'));
    }

    /**
     * Update the specified item in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Item $item)
    {
        $oldQty = $item->quantity;

        // Validate the request data
        $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string|max:1000',
            'price'         => 'required|numeric|min:0',
            'cost'          => 'required|numeric|min:0',
            'quantity'      => 'required|integer|min:0',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active'     => 'boolean',
            'category_id'   => 'required|exists:categories,id',
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
            'name'          => $request->name,
            'description'   => $request->description,
            'price'         => $request->price,
            'cost'          => $request->cost,
            'quantity'      => $request->quantity,
            'is_active'     => $request->boolean('is_active'),
            'category_id'   => $request->category_id,
            'updated_at'    => now(),
        ]);

        $item->refresh();

        // Store to Stock History
        if ($oldQty != $request->quantity) {
            StockHistory::create([
                'item_id'       => $item->id,
                'change'        => $item->quantity - $oldQty,
                'old_quantity'  => $oldQty,
                'new_quantity'  => $item->quantity,
                'reason'        => 'Stock Update',
            ]);
        }

        // Log the update action
        Log::info("Item updated: {$item->name} by user ID: " . Auth::id());

        // Redirect to the items index with a success message
        return redirect()
            ->route('items.index')
            ->with('success', "{$item->name} updated successfully.");
    }

    /**
     * Remove the specified item from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        // Check if the user is authorized to delete the item
        $loggedUser = Auth::user();
        if ($loggedUser->role === UserRole::USER) {
            return redirect()
                ->route('items.index')
                ->with('error', 'You do not have permission to delete this item.');
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
        return redirect()
            ->route('items.index')
            ->with('success', "{$item->name} deleted successfully.");
    }
}
