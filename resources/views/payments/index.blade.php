<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Payment Management</h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between mb-4">
                <div class="flex justify-start gap-4">
                    <form method="GET" action="{{ route('payments.index') }}" class="flex items-center space-x-2">
                        <input type="text" name="filter[search]" value="{{ request('filter.search') }}" placeholder="Search..."
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 placeholder-gray-400">
                        <input type="date" name="filter[start_date]" value="{{ request('filter.start_date') }}"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                        <span>to</span>
                        <input type="date" name="filter[end_date]" value="{{ request('filter.end_date') }}"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Search</button>
                    </form>
                </div>
            </div>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-center rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left">Invoice Number</th>
                            <th scope="col" class="px-6 py-3">Customer</th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="amount" label="Total"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="method" label="Channel"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="status" label="Status"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="created_by" label="Submitted by"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="created_at" label="Submitted on"></x-sort-link></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr data-href="{{ route('payments.show', $payment->id) }}" class="bg-white border-b border-gray-200 hover:bg-gray-50 cursor-pointer">
                            <td class="px-6 py-4 text-left font-semibold">{{ $payment->sale->invoice_number }}</th>
                            <td class="px-6 py-4">{{ $payment->sale->customer_name }}</td>
                            <td class="px-6 py-4">{{ format_rupiah($payment->amount) }}</td>
                            <td class="px-6 py-4">{{ \App\Enums\PaymentMethod::tryFrom($payment->method->value)?->label() ?? 'Unknown' }}</th>
                                @php
                                    $status = \App\Enums\PaymentStatus::tryFrom($payment->status->value);
                                    $badgeClasses = match($status) {
                                        \App\Enums\PaymentStatus::SUCCESS => 'bg-green-100 text-green-800',
                                        \App\Enums\PaymentStatus::PENDING => 'bg-yellow-100 text-yellow-800',
                                        \App\Enums\PaymentStatus::FAILED => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                                @endphp
                            <td class="px-6 py-4">
                                <span class="{{ $badgeClasses }} text-xs font-medium px-2.5 py-1 rounded-full">{{ $status?->label() ?? 'Unknown' }}</span>
                            </td>
                            <td class="px-6 py-4">{{ $payment->createdBy->name }}</td>
                            <td class="px-6 py-4">{{ format_date_with_time($payment->created_at) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500 italic">No payments found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <!-- Pagination -->
                <div>
                    {{ $payments->links() }}
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