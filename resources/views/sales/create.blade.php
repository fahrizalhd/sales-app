<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :menus="[
        ['label' => 'Sales', 'url' => route('sales.index')],
        ['label' => 'Add New Sale']
    ]" />
    </x-slot>

    <div class="max-w-5xl mx-auto p-6 bg-white mt-6 shadow rounded">
        @if(session('error'))
        <div class="mb-4 text-red-500">{{ session('error') }}</div>
        @endif

        <form action="{{ route('sales.store') }}" method="POST" id="saleForm">
            @csrf

            <input type="hidden" name="invoice_number" value="{{ $invoice }}">

            <div class="mb-4">
                <label class="block text-sm font-medium">Invoice Number</label>
                <input type="text" value="{{ $invoice }}" disabled class="w-full border-gray-300 rounded mt-1" />
            </div>

            <div id="item-list">
                <div class="grid grid-cols-6 gap-2 mb-3 item-row">
                    <div class="col-span-3">
                        <label class="block text-sm">Item</label>
                        <select name="items[0][item_id]" class="w-full border rounded px-2 py-1 item-select" required>
                            <option value="">-- Select Item --</option>
                            @foreach ($items as $item)
                            <option value="{{ $item->id }}" data-price="{{ $item->price }}">{{ $item->name }} (Rp{{ number_format($item->price) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm">Qty</label>
                        <input type="number" name="items[0][qty]" class="w-full border rounded px-2 py-1 qty-input" required min="1" value="1">
                    </div>
                    <div>
                        <label class="block text-sm">Subtotal</label>
                        <input type="text" class="w-full border rounded px-2 py-1 subtotal" readonly value="0">
                    </div>
                    <div class="flex items-end">
                        <button type="button" onclick="addItemRow()" class="bg-green-500 text-white px-3 py-1 rounded">+</button>
                    </div>
                </div>
            </div>

            <div class="text-right font-semibold mt-4">
                <span class="mr-2">Total:</span>
                <span id="totalDisplay">Rp0</span>
            </div>

            <div class="flex justify-end mt-6">
                <a href="{{ route('sales.index') }}" class="text-gray-600 px-4 py-2 mr-3">Cancel</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Create</button>
            </div>
        </form>
    </div>

    <script>
        let itemIndex = 1;

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(number);
        }

        function updateSubtotals() {
            let total = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const select = row.querySelector('.item-select');
                const qtyInput = row.querySelector('.qty-input');
                const subtotalInput = row.querySelector('.subtotal');

                const price = parseFloat(select.selectedOptions[0]?.dataset.price || 0);
                const qty = parseInt(qtyInput.value || 0);
                const subtotal = price * qty;

                subtotalInput.value = formatRupiah(subtotal);
                total += subtotal;
            });
            document.getElementById('totalDisplay').innerText = formatRupiah(total);
        }

        function addItemRow() {
            const container = document.getElementById('item-list');
            const row = document.createElement('div');
            row.classList.add('grid', 'grid-cols-6', 'gap-2', 'mb-3', 'item-row');
            row.innerHTML = `
                <div class="col-span-3">
                    <select name="items[${itemIndex}][item_id]" class="w-full border rounded px-2 py-1 item-select" required>
                        <option value="">-- Select Item --</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" data-price="{{ $item->price }}">{{ $item->name }} (Rp{{ number_format($item->price) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <input type="number" name="items[${itemIndex}][qty]" class="w-full border rounded px-2 py-1 qty-input" required min="1" value="1">
                </div>
                <div>
                    <input type="text" class="w-full border rounded px-2 py-1 subtotal" readonly value="0">
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="this.closest('.item-row').remove(); updateSubtotals()" class="bg-red-500 text-white px-3 py-1 rounded">-</button>
                </div>
            `;
            container.appendChild(row);
            itemIndex++;
        }

        // Event delegation
        document.addEventListener('input', function(e) {
            if (e.target.matches('.item-select') || e.target.matches('.qty-input')) {
                updateSubtotals();
            }
        });
    </script>
</x-app-layout>