<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Payment for: {{ $sale->invoice_number }}</h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="overflow-hidden shadow-sm sm:rounded-lg p-4 font-mono text-xs bg-white mx-auto" style="max-width:280px">
                        <div class="text-center border-b border-dashed pb-2 mb-2">
                            <h3 class="font-bold text-base">Maju Mundur</h3>
                            <p class="text-[10px] leading-tight">
                                Jl. Merdeka Raya No. 123, RT 04/RW 05<br>
                                Kelurahan Sukamaju, Kecamatan Sejahtera<br>
                                Kota Bandung, Jawa Barat, 40123<br>
                                Phone: +62 812-3456-7890
                            </p>
                        </div>
                        <div class="mb-2">
                            <p><span class="font-semibold">Invoice:</span> {{ $sale->invoice_number }}</p>
                            <p><span class="font-semibold">Date:</span> {{ format_date_with_time($sale->created_at) }}</p>
                            <p><span class="font-semibold">Customer:</span> {{ $sale->customer_name }}</p>
                        </div>
                        <div class="border-t border-b border-dashed py-2 my-2 grid gap-4">
                            @foreach($sale->saleItems as $saleItem)
                            <div class="flex justify-between items-end">
                                <span class="grid grid-cols-1">
                                    <span>{{ $saleItem->item->name }}</span>
                                    <span>{{ number_format($saleItem->price, 0, ',', '.') }} (x{{ $saleItem->quantity }})</span>
                                </span>
                                <span>{{ number_format($saleItem->price * $saleItem->quantity, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                        <div class="flex justify-between font-bold text-sm border-b border-dashed py-1">
                            <span>Total</span>
                            <span>{{ number_format($sale->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-6 text-center text-xs text-gray-700 border-t border-dashed pt-2">
                            <p>Thank you for shopping with us.</p>
                            <p>Your satisfaction is our priority!</p>
                            <p class="mt-1">See you again!</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 self-start">
                    <form method="POST" action="{{ route('sales.payments.store', $sale) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label for="method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                                <select name="method"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">-- Select Payment Method --</option>
                                    @foreach(\App\Enums\PaymentMethod::cases() as $method)
                                    <option value="{{ $method->value }}">{{ $method->label() }}</option>
                                    @endforeach
                                </select>
                                @error('method') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Amount</label>
                                <input type="number" name="amount"value="{{ old('amount', floatval($sale->total_amount)) }}" required readonly
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="flex items-end justify-end gap-2 mt-4">
                            <a href="{{ route('sales.index') }}"
                                class="py-2.5 px-5  text-sm font-medium text-gray-900 focus:outline-none bg-none rounded-lg hover:bg-gray-100 hover:text-gray-700 focus:z-10 focus:ring-4 focus:ring-gray-100">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center gap-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">
                                <svg class="w-[20px] h-[20px] text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M7 6a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-2v-4a3 3 0 0 0-3-3H7V6Z" clip-rule="evenodd" />
                                    <path fill-rule="evenodd" d="M2 11a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-7Zm7.5 1a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5Z" clip-rule="evenodd" />
                                    <path d="M10.5 14.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z" />
                                </svg>
                                Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>