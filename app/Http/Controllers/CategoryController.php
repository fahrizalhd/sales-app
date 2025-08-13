<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the search term from the request
        $search = $request->input('search');

        // Query the category with optional search functionality
        $categories = Category::when($search, function ($query, $search) {
            return $query->where('name', 'like', '%'. $search. '%')
                ->orWhere('description', 'like', '%'. $search. '%');
        });

        // Sorting
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');

        // Validate sort and direction
        if (in_array($sort, ['name', 'description'])) {
            $categories->orderBy($sort, $direction);
        }

        // Paginate
        $categories = $categories->paginate(10)->withQueryString();

        return view('categories.index', compact('categories'));
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
        $loggedUser = Auth::user();
        if ($loggedUser->role === UserRole::USER) {
            return redirect()->route('items.index')-with('error', 'You do not have permission to delete this category.');
        }

        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('items.index')->with('success', "{$category->name} deleted successfully.");
    }
}
