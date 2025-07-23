<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Pembayaran - {{ $sale->invoice_number }}</h2>
    </x-slot>

    <div class="max-w-xl mx-auto mt-6 bg-white p-6 rounded shadow">
        <form method="POST" action="{{ route('payments.store') }}">
            @csrf
            <input type="hidden" name="sale_id" value="{{ $sale->id }}">

            <div class="mb-4">
                <label class="block font-medium">Total Tagihan</label>
                <input type="text" value="Rp {{ number_format($sale->total_price, 0, ',', '.') }}" class="w-full border px-2 py-1 rounded bg-gray-100" readonly>
            </div>

            <div class="mb-4">
                <label class="block font-medium">Jumlah Bayar</label>
                <input type="number" name="amount" class="w-full border px-2 py-1 rounded" value="{{ $sale->total_price }}" required readonly>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('sales.index') }}" class="text-gray-600 px-4 py-2 mr-2">Cancel</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Bayar</button>
            </div>
        </form>
    </div>
</x-app-layout>
