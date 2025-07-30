<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :menus="[
        ['label' => 'Payments'],
        ['label' => 'Payments List']
    ]" />
    </x-slot>

    <div class="max-w-7xl mx-auto p-6">
        @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
        @endif

        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Recent Payment</h3>
        </div>

        <form method="GET" action="{{ route('payments.index') }}" class="mb-4 flex flex-wrap gap-3 items-center">
            <div>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                    class="border rounded px-2 py-1 text-sm">
            </div>
            <div>
                <p>to</p>
            </div>
            <div>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                    class="border rounded px-2 py-1 text-sm">
            </div>
            <div>
                <button type="submit"
                    class="bg-gray-700 hover:bg-gray-800 text-white text-sm px-3 py-2 rounded">
                    Apply
                </button>
                <a href="{{ route('payments.index') }}"
                    class="text-sm ml-2 text-blue-500 hover:underline">Reset</a>
            </div>
        </form>

        <div class="bg-white shadow rounded">
            <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-2">Payment Code</th>
                        <th class="px-4 py-2">Invoice</th>
                        <th class="px-4 py-2">Date</th>
                        <th class="px-4 py-2">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($payments as $payment)
                    <tr>
                        <td class="px-4 py-2 font-mono">{{ $payment->payment_code }}</td>
                        <td class="px-4 py-2 font-mono">{{ $payment->sale->invoice_number ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $payment->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-2">Rp{{ number_format($payment->amount, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-4 text-center text-gray-500">No payments found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>