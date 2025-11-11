<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class SalesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle
{
    private int $rowNumber = 0;
    private int $month;
    private int $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        return Sale::with(['saleItems.item', 'payments'])
            ->whereMonth('transaction_date', $this->month)
            ->whereYear('transaction_date', $this->year)
            ->orderBy('transaction_date', 'asc')
            ->get();
    }

    public function map($sale): array
    {
        $this->rowNumber++;

        $totalPayment = $sale->payments->sum('amount');
        $latestPayment = $sale->payments->sortByDesc('created_at')->first();

        $items = $sale->saleItems->map(function ($i) {
            return "{$i->item->name} ({$i->quantity}x @" . format_rupiah($i->price) . ")";
        })->implode(', ');

        return [
            $this->rowNumber,
            $sale->invoice_number,
            $sale->customer_name,
            format_date_with_time($sale->transaction_date),
            $items,
            format_rupiah($sale->total_amount),
            $sale->status->label(),
            format_rupiah($totalPayment),
            $latestPayment?->status->label() ?? '-',
            $latestPayment?->method->label() ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Invoice Number',
            'Customer Name',
            'Transaction Date',
            'Items',
            'Total Sale',
            'Sale Status',
            'Total Payment',
            'Payment Status',
            'Payment Channel',
        ];
    }

    public function title(): string 
    {
        return "Sales Report for {$this->month}-{$this->year}";
    }
}
