<pre>{{ $item->image }}</pre>
<pre>{{ asset('storage/' . $item->image) }}</pre>

<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :menus="[
        ['label' => 'Items', 'url' => route('items.index')],
        ['label' => 'Edit Item']
    ]" />
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded shadow">
                <form action="{{ route('items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Code</label>
                        <input type="text" name="code" value="{{ old('code', $item->code) }}" class="w-full mt-1 px-3 py-2 border rounded" required>
                        @error('code') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" value="{{ old('name', $item->name) }}" class="w-full mt-1 px-3 py-2 border rounded" required>
                        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Price</label>
                        <input type="number" name="price" step="0.01" value="{{ old('price', $item->price) }}" class="w-full mt-1 px-3 py-2 border rounded" required>
                        @error('price') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Quantity</label>
                        <input type="number" name="qty" value="{{ old('qty', $item->qty) }}" class="w-full mt-1 px-3 py-2 border rounded" required>
                        @error('qty') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Image</label>
                        <input type="file" name="image" class="mt-1">
                        @if ($item->image)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $item->image) }}" class="w-24 h-24 object-cover rounded border" alt="Current image">
                            <form action="{{ route('items.deleteImage', $item->id) }}" method="POST" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 text-sm hover:underline">
                                    Delete Image
                                </button>
                            </form>
                        </div>
                        @endif
                        @error('image') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('items.index') }}" class="px-4 py-2 text-gray-600 mr-2">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>