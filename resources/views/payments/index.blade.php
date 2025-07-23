<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Payment List</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto p-6">
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow rounded">
            <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-2">Payment Code</th>
                        <th class="px-4 py-2">Invoice</th>
                        <th class="px-4 py-2">Date</th>
                        <th class="px-4 py-2">Amount</th>
                        <!-- <th class="px-4 py-2 text-center">Status</th> -->
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($payments as $payment)
                        <tr>
                            <td class="px-4 py-2 font-mono">{{ $payment->payment_code }}</td>
                            <td class="px-4 py-2 font-mono">{{ $payment->sale->invoice_number ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $payment->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-2">Rp{{ number_format($payment->amount, 0, ',', '.') }}</td>
                            <!-- <td class="px-4 py-2 text-center">
                                @if ($payment->sale->is_paid)
                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Paid</span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Not Paid Yet</span>
                                @endif
                            </td> -->
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
