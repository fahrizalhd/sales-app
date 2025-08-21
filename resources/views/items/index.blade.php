<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Item Management</h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between mb-4">
                <div class="inline-flex justify-start gap-4">
                    <form method="GET" action="{{ route("items.index") }}" class="flex items-center space-x-2">
                        <input type="text" name="filter[search]" value="{{ request("filter.search") }}" placeholder="Search..."
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 placeholder-gray-400" />
                        <button type="submit"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                            Search
                        </button>
                    </form>

                    <div class="flex items-center justify-start gap-2">
                        <span class="font-medium text-sm">Quick Filter:</span>
                        <div x-data="{ lowStock: {{ request("filter.low_stock", 0) ? "true" : "false" }} }">
                            <button @click="
                                lowStock = !lowStock;
                                let url = new URL(window.location.href);
                                url.searchParams.delete('page');
                                if (lowStock) {
                                    url.searchParams.set('filter[low_stock]', '1');
                                } else {
                                    url.searchParams.delete('filter[low_stock]');
                                }
                                window.location.href = url.toString();
                                "
                                :class="lowStock ? 'bg-yellow-200 text-yellow-800' : 'bg-grey-200 text-gray-700 hover:bg-yellow-200 hover:text-yellow-800'"
                                class="px-3 py-2 rounded-full text-xs flex items-center justify center whitespace-nowrap" type="button">
                                Low Stock
                            </button>
                        </div>

                        <div x-data="{ isInactive: {{ request("filter.is_active", 1) == 0 ? "true" : "false" }} }">
                            <button @click="
                                isInactive = !isInactive;
                                let url = new URL(window.location.href);
                                url.searchParams.delete('page');
                                if (isInactive) {
                                    url.searchParams.set('filter[is_active]', '0');
                                } else {
                                    url.searchParams.delete('filter[is_active]');
                                }
                                window.location.href = url.toString();
                                "
                                :class="isInactive ? 'bg-red-200 text-red-600' : 'bg-grey-200 text-gray-700 hover:bg-red-200 hover:text-red-600'"
                                class="px-3 py-2 rounded-full text-xs flex items-center justify center whitespace-nowrap" type="button">
                                Inactive
                            </button>
                        </div>
                    </div>
                </div>

                <a href="{{ route("items.create") }}" 
                    class="flex items-center gap-1 focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5">
                    <svg class="w-5 h-5 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5" />
                    </svg>
                    Add Item
                </a>
            </div>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-center rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <!-- <th scope="col" class="text-right">No</th> -->
                            <th scope="col" class="px-6 py-3 text-left">SKU</th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="name" label="Name"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="category" label="Category"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="price" label="Price"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="quantity" label="Stock"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="is_active" label="Status"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                        <tr
                            data-href="{{ route("items.edit", $item) }}"
                            class="bg-white border-b border-gray-200 hover:bg-gray-50 cursor-pointer">
                            <!-- <td class="text-right">{{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}</td> -->
                            <td class="px-6 py-4 text-left font-bold">{{ $item->sku }}</td>
                            <td class="px-6 py-4">{{ $item->name }}</td>
                            <td class="px-6 py-4">{{ $item->category->name }}</td>
                            <td class="px-6 py-4">{{ format_rupiah($item->price) }}</td>
                            <td class="px-6 py-4">
                                @if ($item->quantity > 10)
                                <span>{{ $item->quantity }}</span>
                                @elseif ($item->quantity <= 10 && $item->quantity > 0)
                                    <span data-tooltip-target="tooltip-{{ $item->id }}-click" data-tooltip-trigger="click"
                                        class="bg-yellow-200 text-yellow-800 inline-flex items-center rounded-sm px-2 py-1 ml-2 cursor-pointer">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M13.09 3.294c1.924.95 3.422 1.69 5.472.692a1 1 0 0 1 1.438.9v9.54a1 1 0 0 1-.562.9c-2.981 1.45-5.382.24-7.25-.701a38.739 38.739 0 0 0-.622-.31c-1.033-.497-1.887-.812-2.756-.77-.76.036-1.672.357-2.81 1.396V21a1 1 0 1 1-2 0V4.971a1 1 0 0 1 .297-.71c1.522-1.506 2.967-2.185 4.417-2.255 1.407-.068 2.653.453 3.72.967.225.108.443.216.655.32Z" />
                                        </svg>
                                        <span class="text-xs ml-1 hidden md:inline">Low Stock</span>
                                    </span>
                                    <div id="tooltip-{{ $item->id }}-click" role="tooltip"
                                        class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-xs opacity-0 tooltip">
                                        Stock: {{ $item->quantity }}
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                    @elseif ($item->quantity === 0)
                                    <span class="bg-red-200 text-red-600 inline-flex items-center rounded-sm px-2 py-1 ml-2">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                                        </svg>
                                        <span class="text-xs ml-1 hidden md:inline">Out of Stock</span>
                                    </span>
                                    @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($item->is_active)
                                <span class="bg-green-100 text-green-800 text-sm font-medium px-2.5 py-1 rounded-full">Active</span>
                                @else
                                <span class="bg-red-100 text-red-800 text-sm font-medium px-2.5 py-1 rounded-full">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route("items.destroy", $item) }}" method="POST" class="inline ml-2"
                                    onsubmit="return confirm('Delete item?')">
                                    @csrf
                                    @method("DELETE")
                                    <button class="text-red-500 p-2">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- Pagination -->
                <div>
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.querySelectorAll('tr[data-href]').forEach((row) => {
        row.addEventListener('click', (e) => {
            // Prevent navigation if clicking on links or buttons or tooltips
            if (!e.target.closest('a, button, [data-tooltip-target]')) {
                window.location.href = row.dataset.href;
            }
        });
    });
</script>