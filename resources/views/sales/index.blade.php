<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Sales Management</h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between mb-4">
                <form method="GET" action="{{ route('sales.index') }}" class="flex items-center space-x-2">
                    <input type="text" name="filter[search]" value="{{ request('filter.search') }}" placeholder="Search..."
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 placeholder-gray-400">
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Search</button>
                </form>

                <a href="{{ route('sales.create') }}" 
                    class="flex items-center gap-1 focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5">
                    <svg class="w-5 h-5 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5" />
                    </svg>
                    Add Sale
                </a>
            </div>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-center rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left"><x-sort-link column="invoice_number" label="Invoice"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="customer_name" label="Customer"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="total_amount" label="Total"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="is_paid" label="Status"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                        <tr data-href="{{ route('sales.edit', $sale) }}" class="bg-white border-b border-gray-200 hover:bg-gray-50 cursor-pointer">
                            <td class="px-6 py-4 text-left font-semibold">{{ $sale->invoice_number }}</th>
                            <td class="px-6 py-4">{{ $sale->customer_name }}</th>
                            <td class="px-6 py-4">{{ format_rupiah($sale->total_amount) }}</td>
                            <td class="px-6 py-4">
                                @if ($sale->is_paid)
                                <span class="bg-green-100 text-green-800 text-sm font-medium px-2.5 py-1 rounded-full">Paid</span>
                                @else
                                <span class="bg-red-100 text-red-800 text-sm font-medium px-2.5 py-1 rounded-full">Not Yet Paid</span>
                                @endif</td>
                            <td class="px-6 py-4">
                                <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete sale?')">
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
                    {{ $sales->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.querySelectorAll('tr[data-href]').forEach(row => {
        row.addEventListener('click', (e) => {
            // Prevent navigation if clicking on links or buttons
            if (!e.target.closest('a, button')) {
                window.location.href = row.dataset.href;
            }
        });
    });
</script>