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
                        <select name="filter[method]"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                            <option value="">All Channel</option>
                            @foreach (\App\Enums\PaymentMethod::cases() as $method)
                            <option value="{{ $method->value }}"
                                @selected(request('filter.method')===$method->value)>
                                {{ $method->label() }}
                            </option>
                            @endforeach
                        </select>
                        <select name="filter[status]"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                            <option value="">All Status</option>
                            @foreach (\App\Enums\PaymentStatus::cases() as $status)
                            <option value="{{ $status->value }}"
                                @selected(request('filter.status')===$status->value)>
                                {{ $status->label() }}
                            </option>
                            @endforeach
                        </select>
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
                            <th scope="col" class="px-6 py-3"><x-sort-link column="amount" label="Amount"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="method" label="Channel"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="status" label="Status"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="created_by" label="Submitter"></x-sort-link></th>
                            <th scope="col" class="px-6 py-3"><x-sort-link column="approved_at" label="Approved at"></x-sort-link></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr data-href="{{ route('payments.show', $payment->id) }}" class="bg-white border-b border-gray-200 hover:bg-gray-50 cursor-pointer">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span>{{ $payment->sale->invoice_number }}</span>
                                    <button type="button" class="copy-btn text-gray-500 hover:text-gray-700" data-clipboard-text="{{ $payment->sale->invoice_number }}">
                                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M9 8v3a1 1 0 0 1-1 1H5m11 4h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1h-7a1 1 0 0 0-1 1v1m4 3v10a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-7.13a1 1 0 0 1 .24-.65L7.7 8.35A1 1 0 0 1 8.46 8H13a1 1 0 0 1 1 1Z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4">{{ $payment->sale->customer_name }}</td>
                            <td class="px-6 py-4">{{ format_rupiah($payment->amount) }}</td>
                            <td class="px-6 py-4">{{ \App\Enums\PaymentMethod::tryFrom($payment->method->value)?->label() ?? 'Unknown' }}</td>
                            @php
                            $status = \App\Enums\PaymentStatus::tryFrom($payment->status->value);
                            $badgeClasses = match($status) {
                                \App\Enums\PaymentStatus::SUCCESS => 'bg-green-100 text-green-800',
                                \App\Enums\PaymentStatus::PENDING => 'bg-yellow-100 text-yellow-800',
                                \App\Enums\PaymentStatus::REJECTED => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                            @endphp
                            <td class="px-6 py-4">
                                <span class="{{ $badgeClasses }} text-xs font-medium px-2.5 py-1 rounded-full">{{ $status?->label() ?? 'Unknown' }}</span>
                            </td>
                            <td class="px-6 py-4">{{ $payment->createdBy->name }} by {{ format_date_with_time($payment->created_at) }}</td>
                            <td class="px-6 py-4">
                                @if ($payment->status == \App\Enums\PaymentStatus::PENDING)
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-1 rounded-full">Not Approved Yet</span>
                                @elseif ($payment->status == \App\Enums\PaymentStatus::SUCCESS)
                                {{ format_date_with_time($payment->approved_at) }}
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500 bg-gray-50 italic">No payments found</td>
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