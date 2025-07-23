<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Sale Detail</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto mt-6 bg-white p-6 rounded shadow">
        <div class="mb-4">
            <p><strong>Invoice:</strong> {{ $sale->invoice_number }}</p>
            <p><strong>Date:</strong> {{ $sale->sale_date }}</p>
            <p><strong>Status:</strong> @if ($sale->is_paid)
                <span class="inline-block px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded">
                    Paid
                </span>
                @else
                <span class="inline-block px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded">
                    Not Yet Paid
                </span>
                @endif
            </p>
        </div>

        <table class="w-full table-auto border-collapse border border-gray-300 mb-4">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2 text-left">Item(s)</th>
                    <th class="border px-4 py-2 text-right">Price</th>
                    <th class="border px-4 py-2 text-center">Qty</th>
                    <th class="border px-4 py-2 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->saleItems as $saleItem)
                <tr>
                    <td class="border px-4 py-2">{{ $saleItem->item->name }}</td>
                    <td class="border px-4 py-2 text-right">{{ number_format($saleItem->price, 2) }}</td>
                    <td class="border px-4 py-2 text-center">{{ $saleItem->qty }}</td>
                    <td class="border px-4 py-2 text-right">{{ number_format($saleItem->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-gray-50 font-semibold">
                    <td colspan="3" class="border px-4 py-2 text-right">Total</td>
                    <td class="border px-4 py-2 text-right">{{ number_format($sale->total_price, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="text-right">
            <a href="{{ route('sales.index') }}" class="text-grey-600 hover:underline">← Back to Sales</a>
        </div>
    </div>
</x-app-layout>