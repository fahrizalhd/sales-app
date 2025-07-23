<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Item List</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
            @endif

            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-700">Items</h3>
                <a href="{{ route('items.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    + Add Item
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-gray-600 font-medium">Image</th>
                            <th class="px-4 py-2 text-left text-gray-600 font-medium">Code</th>
                            <th class="px-4 py-2 text-left text-gray-600 font-medium">Name</th>
                            <th class="px-4 py-2 text-left text-gray-600 font-medium">Price</th>
                            <th class="px-4 py-2 text-left text-gray-600 font-medium">Qty</th>
                            <th class="px-4 py-2 text-left text-gray-600 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($items as $item)
                        <tr>
                            <td class="px-4 py-2">
                                @if ($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="w-12 h-12 object-cover rounded">
                                @else
                                <span class="text-gray-400 italic">No image</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">{{ $item->code }}</td>
                            <td class="px-4 py-2">{{ $item->name }}</td>
                            <td class="px-4 py-2">Rp{{ number_format($item->price, 2, ',', '.') }}</td>
                            <td class="px-4 py-2">{{ $item->qty }}</td>
                            <td class="px-4 py-2 space-x-2">
                                <a href="{{ route('items.edit', $item) }}" class="text-yellow-600 hover:underline">Edit</a>
                                <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Delete this item?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center px-4 py-4 text-gray-500 italic">No items found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>