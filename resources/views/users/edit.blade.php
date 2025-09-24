<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Edit Role: {{ $user->name }}</h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf @method('PUT')

                    <label class="block mb-2 text-sm font-medium text-gray-900">Role:</label>
                    <select name="role" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 mb-4">
                        @foreach($roles as $role)
                        <option value="{{ $role->value }}" @selected($user->role === $role)>
                            {{ $role->label() }}
                        </option>
                        @endforeach
                    </select>

                    <div class="flex justify-end items-center space-x-2">
                        <a href="{{ route('users.index') }}" class="text-sm text-gray-800 px-4 py-2">Back</a>
                        <button class="bg-blue-600 text-white px-4 py-2 rounded text-sm">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>