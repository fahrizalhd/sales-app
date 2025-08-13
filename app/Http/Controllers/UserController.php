<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //  Index method to list users
    public function index(Request $request)
    {
        // Get the search term from the request
        $search = $request->input('search');
        
        // Query the users with optional search functionality
        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('role', 'like', '%' . $search . '%');
        });

        // Sorting logic
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');
        
        // Validate sort and direction
        if (in_array($sort, ['name', 'email', 'role', 'last_login_at'])) {
            $users->orderBy($sort, $direction);
        }

        // Paginate the results
        $users = $users->paginate(10)->withQueryString();
        
        // Return the view with users and search term
        return view('users.index', compact('users', 'search'));
    }

    // Edit method to show user edit form
    public function edit(User $user)
    {
        $loggedUser = Auth::user();
        $loggedUserRole = $loggedUser->role;

        // Role user cannot edit other users
        if ($loggedUserRole === UserRole::USER && $loggedUser->id !== $user->id) {
            return redirect()->route('users.index')->with('error', 'You do not have permission to edit this user.');
        }

        // Admin cannot edit Super Admin users
        if ($loggedUserRole === UserRole::ADMIN && $user->role === UserRole::SUPERADMIN) {
            return redirect()->route('users.index')->with('error', 'You cannot edit a Super Admin user.');
        }

        // Admin cannot edit their own role
        if ($loggedUserRole === UserRole::ADMIN && $loggedUser->id === $user->id) {
            return redirect()->route('users.index')->with('error', 'You cannot edit your own role as an Admin.');
        }

        // User canot edit their own role
        if ($loggedUserRole === UserRole::USER && $loggedUser->id === $user->id) {
            return redirect()->route('users.index')->with('error', 'You cannot edit your own role as a User.');
        }

        // Logic to show the user edit form
        return view('users.edit', [
            'user' => $user,
            'roles' => UserRole::cases(),
        ]);
    }

    // Update method to handle user updates
    public function update(Request $request, User $user)
    {
        // Logic to update the user
        $request->validate([
            'role' => 'required|in:' . implode(',', array_map(fn($role) => $role->value, UserRole::cases())),
        ]);

        $user->role = $request->role;
        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    // Destroy method to handle user deletion
    public function destroy(User $user)
    {
        $loggedUser = Auth::user();
        $loggedUserRole = $loggedUser->role;

        // Role user cannot edit other users
        if ($loggedUserRole === UserRole::USER && $loggedUser->id !== $user->id) {
            return redirect()->route('users.index')->with('error', 'You do not have permission to edit this user.');
        }

        // Admin cannot edit Super Admin users
        if ($loggedUserRole === UserRole::ADMIN && $user->role === UserRole::SUPERADMIN) {
            return redirect()->route('users.index')->with('error', 'You cannot edit a Super Admin user.');
        }

        // Admin cannot edit their own role
        if ($loggedUserRole === UserRole::ADMIN && $loggedUser->id === $user->id) {
            return redirect()->route('users.index')->with('error', 'You cannot edit your own role as an Admin.');
        }
        // Logic to delete the user
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
