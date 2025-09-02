<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt</title>
    <style>
        body {
            font-family: monospace;
            font-size: 12px;
            max-width: 280px;
            margin: 0 auto;
            color: #000;
        }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .line {
            border-top: 1px dashed #000;
            margin: 4px 0;
        }
        .flex-between {
            display: flex;
            justify-content: space-between;
        }
        .mt-2 { margin-top: 8px; }
        .mt-4 { margin-top: 16px; }
    </style>
</head>
<body onload="window.print()">

    <div class="text-center">
        <div class="bold">Maju Mundur</div>
        <div style="font-size: 10px; line-height: 1.2;">
            Jl. Merdeka Raya No. 123<br>
            Kel. Sukamaju, Kec. Sejahtera<br>
            Kota Bandung, Jawa Barat, 40123<br>
            Phone: +62 812-3456-7890
        </div>
    </div>

    <div class="line"></div>

    <div>
        <div>Invoice : {{ $payment->sale->invoice_number }}</div>
        <div>Date    : {{ format_date_with_time($payment->created_at) }}</div>
        <div>Customer: {{ $payment->sale->customer_name }}</div>
    </div>

    <div class="line"></div>

    @foreach($payment->sale->saleItems as $saleItem)
        <div>
            <div>{{ $saleItem->item->name }}</div>
            <div class="flex-between">
                <span>{{ number_format($saleItem->price, 0, ',', '.') }} x{{ $saleItem->quantity }}</span>
                <span>{{ number_format($saleItem->price * $saleItem->quantity, 0, ',', '.') }}</span>
            </div>
        </div>
    @endforeach

    <div class="line"></div>

    <div class="flex-between bold">
        <span>Total</span>
        <span>{{ number_format($payment->sale->total_amount, 0, ',', '.') }}</span>
    </div>

    <div class="flex-between mt-2">
        <span>Method</span>
        <span>{{ $payment->method->label() }}</span>
    </div>

    <div class="line mt-4"></div>

    <div class="text-center" style="font-size: 11px; line-height: 1.4;">
        Terima kasih atas pembelian Anda!<br>
        Kepuasan Anda adalah prioritas kami.<br>
        Sampai jumpa lagi.
    </div>

</body>
</html>
