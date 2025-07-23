<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Add Item</h2>
    </x-slot>

    <div class="max-w-2xl mx-auto mt-6 bg-white p-6 rounded shadow">
        <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block">Name</label>
                <input type="text" name="name" class="w-full border rounded px-2 py-1" required>
            </div>

            <div class="mb-4">
                <label class="block">Price</label>
                <input type="number" name="price" step="0.01" class="w-full border rounded px-2 py-1" required>
            </div>

            <div class="mb-4">
                <label class="block">Qty</label>
                <input type="number" name="qty" class="w-full border rounded px-2 py-1" required>
            </div>

            <div class="mb-4">
                <label class="block">Image</label>
                <input type="file" name="image" class="w-full border rounded px-2 py-1">
            </div>

            <div class="flex justify-end">
                <a href="{{ route('items.index') }}" class="px-4 py-2 text-gray-600 mr-2">Cancel</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            </div>
        </form>
    </div>
</x-app-layout>