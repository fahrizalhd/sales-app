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
                        <table>
                            <tbody>
                                <tr>
                                    <td class="font-semibold">Invoice</td>
                                    <td>:</td>
                                    <td>{{ $payment->sale->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <td class="font-semibold">Date</td>
                                    <td>:</td>
                                    <td>{{ format_date_with_time($payment->created_at) }}</td>
                                </tr>
                                <tr>
                                    <td class="font-semibold">Customer</td>
                                    <td>:</td>
                                    <td>{{ $payment->sale->customer_name }}</td>
                                </tr>
                            </tbody>
                        </table>
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
                        <span>Channel</span>
                        <span>{{ $payment->method->label() }}</span>
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
                        @php
                        $status = \App\Enums\PaymentStatus::tryFrom($payment->status->value);
                        $badgeClasses = match($status) {
                        \App\Enums\PaymentStatus::SUCCESS => 'bg-green-100 text-green-800',
                        \App\Enums\PaymentStatus::PENDING => 'bg-yellow-100 text-yellow-800',
                        \App\Enums\PaymentStatus::REJECTED => 'bg-red-100 text-red-800',
                        default => 'bg-gray-100 text-gray-800',
                        };
                        @endphp
                        <p>
                            <span class="font-medium">Status:</span>
                            <span class="{{ $badgeClasses }} text-xs font-medium px-2.5 py-1 rounded-full">
                                {{ $status?->label() ?? 'Unknown' }}
                            </span>
                        </p>
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
                                    class="inline-flex items-center gap-2 text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">
                                    <svg class="w-[20px] h-[20px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
                                    </svg>
                                    Approve
                                </button>
                            </form>
                            <form action="{{ route('payments.reject', $payment->id) }}" method="POST" onsubmit="return confirm('Reject this payment?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="inline-flex items-center gap-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">
                                    <svg class="w-[20px] h-[20px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm7.707-3.707a1 1 0 0 0-1.414 1.414L10.586 12l-2.293 2.293a1 1 0 1 0 1.414 1.414L12 13.414l2.293 2.293a1 1 0 0 0 1.414-1.414L13.414 12l2.293-2.293a1 1 0 0 0-1.414-1.414L12 10.586 9.707 8.293Z" clip-rule="evenodd"/>
                                    </svg>
                                    Reject
                                </button>
                            </form>
                            @elseif($payment->status === \App\Enums\PaymentStatus::SUCCESS)
                            <form action="{{ route('payments.refund', $payment->id) }}" method="POST" onsubmit="return confirm('Refund this payment?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="inline-flex items-center gap-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">
                                    <svg class="w-[20px] h-[20px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9h13a5 5 0 0 1 0 10H7M3 9l4-4M3 9l4 4"/>
                                    </svg>
                                    Refund
                                </button>
                            </form>
                            @endif
                        </div>
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('payments.index') }}"
                                class="py-2.5 px-5 text-sm font-medium text-gray-900 bg-none rounded-lg hover:bg-gray-100 hover:text-gray-700">
                                Back
                            </a>
                            @if ($payment->status === \App\Enums\PaymentStatus::SUCCESS)
                            <a href="{{ route('payments.print', $payment) }}" target="_blank"
                                class="inline-flex items-center gap-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                                <svg class="w-[20px] h-[20px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M8 3a2 2 0 0 0-2 2v3h12V5a2 2 0 0 0-2-2H8Zm-3 7a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h1v-4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v4h1a2 2 0 0 0 2-2v-5a2 2 0 0 0-2-2H5Zm4 11a1 1 0 0 1-1-1v-4h8v4a1 1 0 0 1-1 1H9Z" clip-rule="evenodd" />
                                </svg>
                                Receipt
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>