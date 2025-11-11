<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Edit Sale: {{ $sale->invoice_number }}</h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                <form method="POST" action="{{ route('sales.update', $sale) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="redirect_to_payment" id="redirect_to_payment" value="0">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Created :</label>
                                <span class="font-semibold text-md">
                                    {{ $sale->createdBy->name }} at {{ format_date_with_time($sale->created_at) }}
                                </span>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Updated :</label>
                                <span class="font-semibold text-md">
                                    {{ $sale->updatedBy->name }} at {{ format_date_with_time($sale->updated_at) }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <label for="customer_name" class="block text-sm font-medium text-gray-700">Customer Name</label>
                            <input type="text" name="customer_name" value="{{ $sale->customer_name }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('customer_name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Items</label>
                            <div class="mt-1 overflow-hidden border rounded-md shadow-sm border-gray-300">
                                <table class="text-sm text-center text-gray-500">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="px-4 py-2 text-sm">Item</th>
                                            <th class="px-2 py-2 text-sm">Qty</th>
                                            <th class="px-4 py-2 text-sm">Price</th>
                                            <th class="px-4 py-2 text-sm">Subtotal</th>
                                            <th class="px-4 py-2 text-sm"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="items-table">
                                        @php
                                            $oldItems = old('items', $sale->saleItems->map(function($si) {
                                                return [
                                                    'id' => $si->item_id,
                                                    'qty' => $si->quantity,
                                                    'price' => $si->price,
                                                ];
                                        })->toArray());
                                        @endphp
                                        @foreach ($oldItems as $index => $oldItem)
                                        <tr>
                                            <td class="px-4 py-2">
                                                <select name="saleItems[{{ $index }}][id]"
                                                    class="item-select border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 md:max-w-[240px] truncate">
                                                    <option value="">-- Select Item --</option>
                                                    @foreach ($items as $item)
                                                    <option value="{{ $item->id }}" data-price="{{ $item->price }}" title="{{ $item->name }} ({{ $item->quantity }})"
                                                        {{ (isset($oldItem['id']) && $oldItem['id'] == $item->id) ? 'selected' : '' }}>
                                                        {{ $item->name }} ({{ $item->quantity }})
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-2 py-2">
                                                <input type="number" name="saleItems[{{ $index }}][qty]" value="{{ $oldItem['qty'] ?? 1 }}" min="1"
                                                    class="item-qty w-16 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            </td>
                                            <td class="px-4 py-2 item-price">{{ format_rupiah($oldItem['price'] ?? 0) }}</td>
                                            <td class="px-4 py-2 item-subtotal">{{ format_rupiah(($oldItem['qty'] ?? 1) * ($oldItem['price'] ?? 0)) }}</td>
                                            <td class="px-0 py-2">
                                                <button type="button" class="remove-row text-red-600">
                                                    <svg class="w-6 h-6 rotate-45" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M5 12h14m-7 7V5" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="flex justify-between items-center">
                                <div>
                                    @error("items")
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="button" id="add-row"
                                    class="mt-2 px-3 py-2 bg-blue-500 hover:bg-blue-600 focus:ring-4 focus:ring-blue-300 text-white rounded-lg text-sm flex items-center gap-2">
                                    <svg class="w-4 h-4 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5" />
                                    </svg>
                                    Add Item
                                </button>
                            </div>
                        </div>
                        <div class="flex justify-end items-center">
                            <span class="text-md font-bold">Total: <span id="total-amount">{{ format_rupiah(0) }}</span></span>
                        </div>
                    </div>
                    <div class="flex items-end justify-end gap-2 mt-4">
                        <a href="{{ route('sales.index') }}"
                            class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-none rounded-lg hover:bg-gray-100 hover:text-gray-700 focus:z-10 focus:ring-4 focus:ring-gray-100">
                            Back
                        </a>
                        @if (in_array($sale->status->value, \App\Models\Sale::canBeDeleted()))                  
                        <button type="submit" class="inline-flex items-center gap-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none"
                            form="cancel-sale-{{ $sale->id }}" onclick="return confirm('Cancel this sale?')">
                            <svg class="w-[20px] h-[20px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                            </svg>
                            Cancel
                        </button>
                        @endif
                        @if (in_array($sale->status->value, \App\Models\Sale::canBePaid()))                  
                        <button type="submit" onclick="document.getElementById('redirect_to_payment').value='1'"
                            class="inline-flex items-center gap-2 text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">
                            <svg class="w-[20px] h-[20px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M7 6a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-2v-4a3 3 0 0 0-3-3H7V6Z" clip-rule="evenodd"/>
                                <path fill-rule="evenodd" d="M2 11a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-7Zm7.5 1a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5Z" clip-rule="evenodd"/>
                                <path d="M10.5 14.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z"/>
                            </svg>
                            Pay
                        </button>
                        @endif
                        <button type="submit"
                            class="inline-flex items-center gap-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">
                            <svg class="w-[20px] h-[20px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm10 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM8 7V5h8v2a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1Z"
                                    clip-rule="evenodd" />
                            </svg>
                            Update
                        </button>
                    </div>
                </form>

                @if (in_array($sale->status->value, \App\Models\Sale::canBeDeleted()))
                <form id="cancel-sale-{{ $sale->id }}" action="{{ route('sales.cancel', $sale->id) }}"  method="POST" class="hidden">
                    @csrf
                    @method('PATCH')
                </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function formatRupiah(nominal) {
        return "Rp" + new Intl.NumberFormat('id-ID').format(nominal);
    }

    let rowIndex = document.querySelectorAll("#items-table tr").length;

    function updateRow(row) {
        let select = row.querySelector(".item-select");
        let qtyInput = row.querySelector(".item-qty");
        let priceCell = row.querySelector(".item-price");
        let subtotalCell = row.querySelector(".item-subtotal");

        let price = parseInt(select.selectedOptions[0]?.dataset.price || 0);
        let qty = parseInt(qtyInput.value || 0);
        let subtotal = price * qty;

        priceCell.textContent = formatRupiah(price);
        subtotalCell.textContent = formatRupiah(subtotal);

        updateTotal();
    }

    function updateTotal() {
        let total = 0;
        document.querySelectorAll(".item-subtotal").forEach(cell => {
            let val = cell.textContent.replace(/[^\d]/g, "");
            total += val ? parseInt(val) : 0;
        });
        document.getElementById("total-amount").textContent = formatRupiah(total);
    }

    document.querySelectorAll("#items-table tr").forEach(row => {
        row.querySelector(".item-select").addEventListener("change", () => updateRow(row));
        row.querySelector(".item-qty").addEventListener("input", () => updateRow(row));
    });

    document.getElementById("add-row").addEventListener("click", () => {
        let tbody = document.getElementById("items-table");
        let newRow = document.createElement("tr");
        newRow.innerHTML = `
            <td class="px-4 py-2">
                <select name="saleItems[${rowIndex}][id]" 
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 item-select md:max-w-[240px] truncate">
                    <option value="">-- Select Item --</option>
                    @foreach ($items as $item)
                    <option value="{{ $item->id }}" data-price="{{ $item->price }}" title="{{ $item->name }} ({{ $item->quantity }})">
                        {{ $item->name }} ({{ $item->quantity }})
                    </option>
                    @endforeach
                </select>
            </td>
            <td class="px-2 py-2">
                <input type="number" name="saleItems[${rowIndex}][qty]" value="1" min="1"
                    class="w-16 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 item-qty">
            </td>
            <td class="px-4 py-2 item-price">{{ format_rupiah(0) }}</td>
            <td class="px-4 py-2 item-subtotal">{{ format_rupiah(0) }}</td>
            <td class="px-0 py-2">
                <button type="button" class="remove-row text-red-600">
                    <svg class="w-6 h-6 rotate-45" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5" />
                    </svg>
                </button>
            </td>
        `;
        tbody.appendChild(newRow);

        newRow.querySelector(".item-select").addEventListener("change", () => updateRow(newRow));
        newRow.querySelector(".item-qty").addEventListener("input", () => updateRow(newRow));
        newRow.querySelector(".remove-row").addEventListener("click", () => {
            newRow.remove();
            updateTotal();
        });

        rowIndex++;
    });

    document.querySelectorAll(".remove-row").forEach(btn => {
        btn.addEventListener("click", function() {
            this.closest("tr").remove();
            updateTotal();
        });
    });

    updateTotal();
</script>