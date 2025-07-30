<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :menus="[
        ['label' => 'Sales'],
        ['label' => 'Sales List']
    ]" />
    </x-slot>

    <div class="max-w-7xl mx-auto p-6">
        @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
        @endif

        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Recent Sales</h3>
            <a href="{{ route('sales.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                + Create Sale
            </a>
        </div>

        <form method="GET" action="{{ route('sales.index') }}" class="mb-4 flex flex-wrap gap-3 items-center">
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
                <a href="{{ route('sales.index') }}"
                    class="text-sm ml-2 text-blue-500 hover:underline">Reset</a>
            </div>
        </form>

        <div class="bg-white shadow rounded">
            <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50 text-center">
                    <tr>
                        <th class="px-4 py-2 text-left">Invoice</th>
                        <th class="px-4 py-2">Date</th>
                        <th class="px-4 py-2">Total Price</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-center">
                    @forelse ($sales as $sale)
                    <tr>
                        <td class="px-4 py-2 font-mono text-left">
                            <a href="{{ route('sales.show', $sale->id) }}" class="text-blue-600 hover:underline">
                                {{ $sale->invoice_number }}
                            </a>
                        </td>
                        <td class="px-4 py-2">{{ $sale->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-2">Rp{{ number_format($sale->total_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-2">
                            @if ($sale->is_paid)
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Paid</span>
                            @else
                            <a href="{{ route('payments.create', $sale->id) }}" class="text-blue-600 hover:underline">Pay Now</a>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            @if (!$sale->is_paid)
                            <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline ml-2">Delete</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">No sales recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>