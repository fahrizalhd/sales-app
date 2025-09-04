<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Payment for: {{ $payment->sale->invoice_number }}</h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                        <p><span class="font-semibold">Invoice:</span> {{ $payment->sale->invoice_number }}</p>
                        <p><span class="font-semibold">Date:</span> {{ format_date_with_time($payment->created_at) }}</p>
                        <p><span class="font-semibold">Customer:</span> {{ $payment->sale->customer_name }}</p>
                    </div>
                    <div class="border-t border-b border-dashed py-2 my-2 grid gap-4">
                        @foreach($payment->sale->saleItems as $saleItem)
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
                        <span>{{ number_format($payment->sale->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="mt-2 flex justify-between text-xs">
                        <span>Method: {{ $payment->method }}</span>
                    </div>
                    <div class="mt-6 text-center text-xs text-gray-700 border-t border-dashed pt-2">
                        <p>Thank you for shopping with us.</p>
                        <p>Your satisfaction is our priority!</p>
                        <p class="mt-1">See you again!</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 self-start">
                    <div>
                        <h3 class="text-md font-semibold mb-2">Sale Information</h3>
                        <p><span class="font-medium">Invoice:</span> {{ $payment->sale->invoice_number }}</p>
                        <p><span class="font-medium">Customer:</span> {{ $payment->sale->customer_name }}</p>
                        <p><span class="font-medium">Total Amount:</span> Rp {{ number_format($payment->sale->total_amount, 0, ',', '.') }}</p>
                    </div>
                    <div class="pt-4">
                        <h3 class="text-md font-semibold mb-2">Payment Information</h3>
                        <p><span class="font-medium">Method:</span> {{ $payment->method->label() }}</p>
                        <p><span class="font-medium">Amount:</span> Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                        <p><span class="font-medium">Status:</span> {{ $payment->status->label() }}</p>
                        <p><span class="font-medium">Created at:</span> {{ format_date_with_time($payment->created_at) }}</p>
                        <p><span class="font-medium">Submitted by:</span> {{ $payment->createdBy->name }}</p>
                    </div>
                    <div class="flex justify-between gap-2 mt-4">
                        <div class="flex justify-start gap-2">
                            @if ($payment->status === \App\Enums\PaymentStatus::PENDING)
                            <form action="{{ route('payments.approve', $payment->id) }}" method="POST" onsubmit="return confirm('Approve this payment?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="py-2.5 px-5 text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 rounded-lg">
                                    Approve
                                </button>
                            </form>
                            <form action="{{ route('payments.reject', $payment->id) }}" method="POST" onsubmit="return confirm('Reject this payment?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="py-2.5 px-5 text-sm font-medium text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 rounded-lg">
                                    Reject
                                </button>
                            </form>
                            @endif
                        </div>
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('payments.index') }}"
                                class="py-2.5 px-5 text-sm font-medium text-gray-900 bg-none rounded-lg hover:bg-gray-100 hover:text-gray-700">
                                Back
                            </a>
                            <a href="{{ route('payments.print', $payment) }}" target="_blank"
                                class="inline-flex items-center gap-2 text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5">
                                Print Receipt
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>