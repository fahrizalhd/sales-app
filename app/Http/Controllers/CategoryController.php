<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = QueryBuilder::for(Category::class)
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $query
                        ->where('name', 'like', "%{$value}%")
                        ->orWhere('description', 'like', "%{$value}%");
                }),
            ])
            ->allowedSorts(['name', 'description'])
            ->defaultSort('name')
            ->paginate(10)
            ->withQueryString();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // User can't add item
        $loggedUserRole = Auth::user()->role;
        if ($loggedUserRole === UserRole::USER) {
            return redirect()
                ->route('categories.index')
                ->with('error', 'You do not have permission to add new item.');
        }

        // Return the view for creating a new item
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $category = Category::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        return redirect()->route('categories.index')->with('success', "{$category->name} created successfully");
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
        $loggedUserRole = Auth::user()->role;
        if ($loggedUserRole === UserRole::USER) {
            return redirect()
                ->route('categories.index')
                ->with('error', 'You do not have permission to edit this item.');
        }

        $category = Category::findOrFail($id);

        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);


        $category->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        return redirect()->route('categories.index')->with('success', "{$category->name} updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $loggedUser = Auth::user();
        if ($loggedUser->role === UserRole::USER) {
            return redirect()->route('items.index') -
                with('error', 'You do not have permission to delete this category.');
        }

        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', "{$category->name} deleted successfully.");
    }
}
