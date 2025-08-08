<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Item Management</h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between mb-4">
                <form method="GET" action="{{ route('items.index') }}" class="flex items-center space-x-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 placeholder-gray-400">
                    <select name="status" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                        <option value="">All Status</option>
                        <option value="1" @selected(request('status')==='1' )>Active</option>
                        <option value="0" @selected(request('status')==='0' )>Inactive</option>
                    </select>

                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Search</button>
                </form>
            </div>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-center rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="text-right">No</th>
                            <th scope="col" class="px-6 py-3 text-left">{!! sortableColumn('name', 'Name') !!}</th>
                            <th scope="col" class="px-6 py-3">SKU</th>
                            <th scope="col" class="px-6 py-3">{!! sortableColumn('price', 'Price') !!}</th>
                            <th scope="col" class="px-6 py-3">{!! sortableColumn('quantity', 'Stock') !!}</th>
                            <th scope="col" class="px-6 py-3">{!! sortableColumn('Status', 'Status') !!}</th>
                            <th scope="col" class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr class="bg-white border-b border-gray-200 hover:bg-gray-50">
                            <td class="text-right">{{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}</td>
                            <td class="px-6 py-4 text-left">
                                <span class="font-bold">{{ $item->name }}</span>
                                @if ($item->quantity <= 20)
                                <span class="bg-yellow-200 text-yellow-600 inline-flex items-center rounded-sm px-2 py-0.5 ml-4">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M13.09 3.294c1.924.95 3.422 1.69 5.472.692a1 1 0 0 1 1.438.9v9.54a1 1 0 0 1-.562.9c-2.981 1.45-5.382.24-7.25-.701a38.739 38.739 0 0 0-.622-.31c-1.033-.497-1.887-.812-2.756-.77-.76.036-1.672.357-2.81 1.396V21a1 1 0 1 1-2 0V4.971a1 1 0 0 1 .297-.71c1.522-1.506 2.967-2.185 4.417-2.255 1.407-.068 2.653.453 3.72.967.225.108.443.216.655.32Z" />
                                    </svg>
                                    <span class="text-xs ml-1">Low Stock</span>
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $item->sku }}</td>
                            <td class="px-6 py-4">{{ format_rupiah($item->price)}}</td>
                            <td class="px-6 py-4">{{ $item->quantity }}</td>
                            <td class="px-6 py-4">
                                @if($item->is_active)
                                <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full">Active</span>
                                @else
                                <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('items.edit', $item) }}" class="font-medium text-blue-600 hover:underline">Edit</a>
                                <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete item?')">
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
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>