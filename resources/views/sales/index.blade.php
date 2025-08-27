<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Sales Management</h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between mb-4">
                <div class="flex justify-start gap-4">
                    <form method="GET" action="{{ route('sales.index') }}" class="flex items-center space-x-2">
                        <input type="text" name="filter[search]" value="{{ request('filter.search') }}" placeholder="Search..."
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 placeholder-gray-400">
                        <input type="date" name="filter[start_date]" value="{{ request('filter.start_date') }}"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                        <span>to</span>
                        <input type="date" name="filter[end_date]" value="{{ request('filter.end_date') }}"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Search</button>
                    </form>

                    <div class="flex items-center justify-start gap-2">
                        <span class="font-medium text-sm">Quick Filter:</span>
                        <div x-data="{ isUnpaid: {{ request("filter.is_paid", 1) == 0 ? "true" : "false" }} }">
                            <button @click="
                                isUnpaid = !isUnpaid;
                                let url = new URL(window.location.href);
                                url.searchParams.delete('page');
                                if (isUnpaid) {
                                    url.searchParams.set('filter[is_paid]', '0');
                                } else {
                                    url.searchParams.delete('filter[is_paid]');
                                }
                                window.location.href = url.toString();
                                "
                                :class="isUnpaid ? 'bg-red-200 text-red-600' : 'bg-grey-200 text-gray-700 hover:bg-red-200 hover:text-red-600'"
                                class="px-3 py-2 rounded-full text-xs flex items-center justify center whitespace-nowrap" type="button">
                                Not Yet Paid
                            </button>
                        </div>
                    </div>
                </div>

                <a href="{{ route('sales.create') }}"
                    class="flex items-center gap-1 focus:outline-none text-white bg-green-600 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5">
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
                            <th scope="col" class="px-6 py-3 text-left">Invoice Number</th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="customer_name" label="Customer"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="created_at" label="Date"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="total_amount" label="Total"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="is_paid" label="Status"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                        <tr data-href="{{ route('sales.edit', $sale) }}" class="bg-white border-b border-gray-200 hover:bg-gray-50 cursor-pointer">
                            <td class="px-6 py-4 text-left font-semibold">{{ $sale->invoice_number }}</th>
                            <td class="px-6 py-4">{{ $sale->customer_name }}</th>
                            <td class="px-6 py-4 font-semibold">{{ format_date_with_time($sale->created_at) }}</th>
                            <td class="px-6 py-4">{{ format_rupiah($sale->total_amount) }}</td>
                            <td class="px-6 py-4">
                                @if ($sale->is_paid)
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-1 rounded-full">Paid</span>
                                @else
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-1 rounded-full">Not Yet Paid</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete sale?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 italic">No sales found</td>
                        </tr>
                        @endforelse
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