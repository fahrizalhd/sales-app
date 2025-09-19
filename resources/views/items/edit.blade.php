<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Edit Item: {{ $item->sku }}</h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <form method="POST" action="{{ route('items.update', $item) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Item Name</label>
                                <input type="text" name="name" id="name" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    value="{{ old('name', $item->name) }}">
                                @error('name')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                                <select name="category_id" id="category_id" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                                <input type="text" name="sku" id="sku" required readonly
                                    class="mt-1 block w-full disabled border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    value="{{ old('sku', $item->sku) }}">
                                @error('sku')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                        Rp
                                    </div>
                                    <input type="number" name="price" id="price" required step=10
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 ps-10"
                                        value="{{ old('price', number_format($item->price, 0, '', '')) }}">
                                    @error('price')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 items-center">
                                <div>
                                    <label for="quantity" class="block text-sm font-medium text-gray-700">Stock Quantity</label>
                                    <input type="number" name="quantity" id="quantity" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        value="{{ old('quantity', $item->quantity) }}">
                                    @error('quantity')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mt-4">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" id="is_active" value="1"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                        {{ old('is_active', $item->is_active) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </div>
                            </div>
                            <div class="md:relative md:h-[66px]">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" id="description" rows="4"
                                    class="mt-1 block md:absolute md:top-6 md:right-0 md:bottom-0 md:left-0 w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $item->description) }}</textarea>
                                @error('description')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
                                <div class="flex flex-col items-center justify-center w-full mt-1">
                                    <label for="dropzone-file"
                                        class="group relative flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 overflow-hidden">
                                        <div id="dropzone-content"
                                            class="flex flex-col items-center justify-center pt-5 pb-6 {{ $item->image_path ? 'hidden' : '' }}">
                                            <svg class="w-8 h-8 mb-4 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 20 16">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M13 13h3a3 3 0 000-6h-.025A5.56 5.56 0 0016 6.5A5.5 5.5 0 005.207 5.021 C5.137 5.017 5.071 5 5 5a4 4 0 000 8h2.167 M10 15V6m0 0L8 8m2-2 2 2" />
                                            </svg>
                                            <p class="mb-2 text-sm text-gray-500">
                                                <span class="font-semibold">Click to upload</span> or drag and drop
                                            </p>
                                            <p class="text-xs text-gray-500">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
                                        </div>
                                        <img id="image-preview"
                                            src="{{ $item->image_path ? asset('storage/' . $item->image_path) : '' }}"
                                            alt="Item Image"
                                            class="max-h-32 py-2 rounded-md {{ $item->image_path ? '' : 'hidden' }}">
                                        <button type="button" onclick="removeImage()"
                                            class="absolute top-2 right-2 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center cursor-pointer {{ $item->image_path ? '' : 'hidden' }}">
                                            <svg class="w-4 h-4 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <input type="hidden" name="delete_image" id="delete_image" value="0">
                                        <input id="dropzone-file" type="file" name="image" accept=".jpg,.jpeg,.png,.gif"
                                            class="hidden">
                                    </label>
                                </div>
                                @error('image')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="flex items-end justify-end gap-2 mt-4">
                            <a href="{{ route('items.index') }}"
                                class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-none rounded-lg hover:bg-gray-100 hover:text-gray-700 focus:z-10 focus:ring-4 focus:ring-gray-100">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex item-center gap-2 py-2.5 px-5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none rounded-lg focus:ring-4 focus:ring-blue-300">
                                <svg class="w-[20px] h-[20px] text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm10 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM8 7V5h8v2a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1Z" clip-rule="evenodd" />
                                </svg>
                                Update
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 max-h-[56vh] overflow-y-auto">
                    <div class="flex items-center justify-start mb-2">
                        <span class="text-md font-bold leading-none text-gray-900">Stock History</span>
                    </div>
                    <div class="flow-root">
                        <ul role="list" class="divide-y divide-gray-200">
                            @forelse ($item->stockHistories as $history)
                            <li class="py-3 sm:py-4">
                                <div class="flex items-center">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $history->reason }}
                                        </p>
                                        <p class="text-xs text-gray-500 truncate">
                                            {{ $history->createdBy?->name }} ({{ $history->createdBy?->role }})
                                        </p>
                                    </div>
                                    @php
                                    $stock_changes = $history->new_quantity - $history->old_quantity;
                                    $class = $stock_changes > 0 ? 'text-green-600' : 'text-red-600';
                                    @endphp
                                    <div class="inline-flex items-center text-base font-semibold {{ $class }}">
                                        {{ $stock_changes > 0 ? '+' : '' }}{{ $stock_changes }}
                                    </div>
                                </div>
                            </li>
                            @empty
                            <p class="flex items-center justify-center text-gray-500 italic mt-6 mb-6">No stock changes recorded.</p>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
</x-app-layout>

<script>
    const fileInput = document.getElementById('dropzone-file');
    const preview = document.getElementById('image-preview');
    const dropzoneContent = document.getElementById('dropzone-content');

    fileInput.addEventListener('change', function(event) {
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                dropzoneContent.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            preview.src = '';
            preview.classList.add('hidden');
            dropzoneContent.classList.remove('hidden');
        }
    });

    function removeImage() {
        const preview = document.getElementById('image-preview');
        const dropzoneContent = document.getElementById('dropzone-content');
        const fileInput = document.getElementById('dropzone-file');
        const deleteInput = document.getElementById('delete_image');

        preview.src = '';
        preview.classList.add('hidden');
        dropzoneContent.classList.remove('hidden');
        fileInput.value = '';
        deleteInput.value = '1'; // Set to indicate image deletion
    }
</script>