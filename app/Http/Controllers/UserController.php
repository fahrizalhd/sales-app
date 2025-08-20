<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    /**
     * Display a listing of the users with filtering and sorting.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = QueryBuilder::for(User::class)
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $query
                        ->where('name', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%")
                        ->orWhere('role', 'like', "%{$value}%");
                }),
            ])
            ->allowedSorts(['name', 'email', 'role', 'last_login_at'])
            ->defaultSort('name')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(User $user)
    {
        $loggedUser = Auth::user();
        $loggedUserRole = $loggedUser->role;

        // Role user cannot edit other users
        if ($loggedUserRole === UserRole::USER && $loggedUser->id !== $user->id) {
            return redirect()
                ->route('users.index')
                ->with('error', 'You do not have permission to edit this user.');
        }

        // Admin cannot edit Super Admin users
        if ($loggedUserRole === UserRole::ADMIN && $user->role === UserRole::SUPERADMIN) {
            return redirect()
                ->route('users.index')
                ->with('error', 'You cannot edit a Super Admin user.');
        }

        // Admin cannot edit their own role
        if ($loggedUserRole === UserRole::ADMIN && $loggedUser->id === $user->id) {
            return redirect()
                ->route('users.index')
                ->with('error', 'You cannot edit your own role as an Admin.');
        }

        // User canot edit their own role
        if ($loggedUserRole === UserRole::USER && $loggedUser->id === $user->id) {
            return redirect()
                ->route('users.index')
                ->with('error', 'You cannot edit your own role as a User.');
        }

        // Logic to show the user edit form
        return view('users.edit', [
            'user' => $user,
            'roles' => UserRole::cases(),
        ]);
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        // Logic to update the user
        $request->validate([
            'role' =>
                'required|in:' .
                implode(',', array_map(fn($role) => $role->value, UserRole::cases())),
        ]);

        $user->role = $request->role;
        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */ public function destroy(User $user)
    {
        $loggedUser = Auth::user();
        $loggedUserRole = $loggedUser->role;

        // Role user cannot edit other users
        if ($loggedUserRole === UserRole::USER && $loggedUser->id !== $user->id) {
            return redirect()
                ->route('users.index')
                ->with('error', 'You do not have permission to edit this user.');
        }

        // Admin cannot edit Super Admin users
        if ($loggedUserRole === UserRole::ADMIN && $user->role === UserRole::SUPERADMIN) {
            return redirect()
                ->route('users.index')
                ->with('error', 'You cannot edit a Super Admin user.');
        }

        // Admin cannot edit their own role
        if ($loggedUserRole === UserRole::ADMIN && $loggedUser->id === $user->id) {
            return redirect()
                ->route('users.index')
                ->with('error', 'You cannot edit your own role as an Admin.');
        }
        // Logic to delete the user
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
