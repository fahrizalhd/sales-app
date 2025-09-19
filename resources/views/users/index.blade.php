<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">User Management</h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between mb-4">
                <form method="GET" action="{{ route('users.index') }}" class="flex items-center space-x-2">
                    <input type="text" name="filter[search]" value="{{ request('filter.search') }}" placeholder="Search..."
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 placeholder-gray-400">
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Search</button>
                </form>
            </div>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-center rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="name" label="Name"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="email" label="Email"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="role" label="Role"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="last_login_at" label="Last Login"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr data-href="{{ route('users.edit', $user) }}" class="bg-white border-b border-gray-200 hover:bg-gray-50 cursor-pointer">
                            <td class="px-6 py-4">{{ $user->name }}</th>
                            <td class="px-6 py-4">{{ $user->email }}</td>
                            <td class="px-6 py-4">{{ \App\Enums\UserRole::tryFrom($user->role->value)?->label() ?? 'Unknown' }}</td>
                            <td class="px-6 py-4">
                                @if($user->last_login_at)
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium inline-flex items-center gap-2 px-2 py-1 rounded-sm">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>{{ $user->last_login_at->diffForHumans() }}</span>
                                </span>
                                @else
                                <span class="text-gray-400 italic">Never</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete user?')">
                                    @csrf @method('DELETE')
                                    <button class="inline-flex items-center justify-center p-2 text-red-500 rounded-full hover:bg-red-100 hover:text-red-600 transition-colors duration-200">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500 bg-gray-50 italic">No users found</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <!-- Pagination -->
                <div>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.querySelectorAll('tr[data-href]').forEach(row => {
        row.addEventListener('click', (e) => {
            if (!e.target.closest('a, button')) { // Prevent navigation if clicking on links or buttons
                window.location.href = row.dataset.href;
            }
        });
    });
</script>