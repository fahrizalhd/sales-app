<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Sales Management</h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <form method="GET" action="{{ route('sales.index') }}"
                    class="flex flex-wrap items-center gap-2 flex-1">
                    <input type="text" name="filter[search]" value="{{ request('filter.search') }}" placeholder="Search..."
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 placeholder-gray-400">
                    <input type="date" name="filter[start_date]" value="{{ request('filter.start_date') }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                    <span>to</span>
                    <input type="date" name="filter[end_date]" value="{{ request('filter.end_date') }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                    <select name="filter[status]"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                        <option value="">All Status</option>
                        @foreach (\App\Enums\SaleStatus::cases() as $status)
                        <option value="{{ $status->value }}"
                            @selected(request('filter.status')===$status->value)>
                            {{ $status->label() }}
                        </option>
                        @endforeach
                    </select>
                    <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                        Search
                    </button>
                </form>
                <div class="flex justify-end md:justify-start">
                    <a href="{{ route('sales.create') }}"
                        class="flex items-center gap-1 focus:outline-none text-white bg-green-600 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 md:h-11">
                        <svg class="w-5 h-5 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 12h14m-7 7V5" />
                        </svg>
                        Add Sale
                    </a>
                </div>
            </div>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-center rtl:text-right text-gray-500 overflow-hidden">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left">Invoice Number</th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="customer_name" label="Customer"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="transaction_date" label="Date"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="total_amount" label="Amount"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="status" label="Status"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="created_by" label="Handler"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                        <tr data-href="{{ route('sales.edit', $sale) }}" class="bg-white border-b border-gray-200 hover:bg-gray-50 cursor-pointer">
                            <td class="px-6 py-4 text-left font-semibold">{{ $sale->invoice_number }}</th>
                            <td class="px-6 py-4">{{ $sale->customer_name }}</th>
                            <td class="px-6 py-4 font-semibold">{{ format_date_with_time($sale->transaction_date) }}</th>
                            <td class="px-6 py-4">{{ format_rupiah($sale->total_amount) }}</td>
                            @php
                            $colors = [
                            \App\Enums\SaleStatus::PAID->value => 'bg-green-100 text-green-800',
                            \App\Enums\SaleStatus::PARTIALLY_PAID->value => 'bg-blue-100 text-blue-800',
                            \App\Enums\SaleStatus::UNPAID->value => 'bg-yellow-100 text-yellow-800',
                            \App\Enums\SaleStatus::NEED_REVIEW->value => 'bg-orange-100 text-orange-800',
                            \App\Enums\SaleStatus::CANCELLED->value => 'bg-red-100 text-red-800',
                            ];
                            @endphp
                            <td class="px-6 py-4">
                                <span class="{{ $colors[$sale->status->value] }} text-xs font-medium px-2.5 py-1 rounded-full">
                                    {{ $sale->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ $sale->createdBy->name }}</th>
                            <td class="px-6 py-4">
                                <div class="flex justify-center items-center gap-2">
                                    @if ($sale->status === \App\Enums\SaleStatus::UNPAID || $sale->status === \App\Enums\SaleStatus::PARTIALLY_PAID)
                                    <a href="{{ route('sales.payments.create', $sale->id) }}"
                                        class="inline-flex items-center justify-center p-2 text-blue-500 rounded-full hover:bg-blue-100 hover:text-blue-600 transition-colors duration-200">
                                        Pay
                                    </a>
                                    @endif
                                    <form action="{{ route('sales.destroy', $sale) }}" method="POST"
                                        onsubmit="return confirm('Delete sale?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center justify-center p-2 text-red-500 rounded-full hover:bg-red-100 hover:text-red-600 transition-colors duration-200">
                                            Delete
                                        </button>
                                    </form>
                                </div>
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