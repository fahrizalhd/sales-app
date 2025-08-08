<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">User Management</h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between mb-4">
                <form method="GET" action="{{ route('users.index') }}" class="flex items-center space-x-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 placeholder-gray-400">
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Search</button>
                </form>
            </div>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-center rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left">{!! sortableColumn('name', 'Name') !!}</th>
                            <th scope="col" class="px-6 py-3">{!! sortableColumn('email', 'Email') !!}</th>
                            <th scope="col" class="px-6 py-3">{!! sortableColumn('role', 'Role') !!}</th>
                            <th scope="col" class="px-6 py-3">{!! sortableColumn('last_login_at', 'Last Login') !!}</th>
                            <th scope="col" class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr class="bg-white border-b border-gray-200 hover:bg-gray-50">
                            <td class="px-6 py-4 text-left">{{ $user->name }}</th>
                            <td class="px-6 py-4">{{ $user->email }}</td>
                            <td class="px-6 py-4">{{ \App\Enums\UserRole::tryFrom($user->role->value)?->label() ?? 'Unknown' }}</td>
                            <td class="px-6 py-4">
                                @if($user->last_login_at)
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded-sm me-2">
                                    <svg class="w-2.5 h-2.5 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm3.982 13.982a1 1 0 0 1-1.414 0l-3.274-3.274A1.012 1.012 0 0 1 9 10V6a1 1 0 0 1 2 0v3.586l2.982 2.982a1 1 0 0 1 0 1.414Z" />
                                    </svg>
                                    {{ $user->last_login_at->diffForHumans() }}
                                </span>
                                @else
                                <span class="text-gray-400 italic">Never</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('users.edit', $user) }}" class="font-medium text-blue-600 hover:underline">Edit</a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete user?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
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