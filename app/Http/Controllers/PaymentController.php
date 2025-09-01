<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Item;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;

class PaymentController extends Controller
{
    public function create(string $id)
    {
        $sale = Sale::with('saleItems.item')->findOrFail($id);

        return view('payments.create', compact('sale'));
    }

    public function store(Request $request, Sale $sale)
    {
        $request->validate([
            'method' => ['required', new Enum(PaymentMethod::class)],
            'amount' => 'required|numeric|min:0',
        ]);

        $status = $request->method === PaymentMethod::CASH->value
            ? PaymentStatus::SUCCESS->value
            : PaymentStatus::PENDING->value;

        $loggedUser = Auth::user();

        DB::transaction(function () use ($request, $sale, $loggedUser, $status) {
            Payment::create([
                'sale_id'       => $sale->id,
                'amount'        => $request->amount,
                'method'        => $request->method,
                'status'        => $status,
                'created_by'    => $loggedUser->id,
                'updated_by'    => $loggedUser->id,
            ]);

            $alreadyAdjusted = StockHistory::WhereIn('item_id', $sale->saleItems()->pluck('item_id'))
                ->where('reason', "Sale #{$sale->invoice_number}")
                ->exists();
            if ($alreadyAdjusted) {
                throw new \RuntimeException("Stock for this sale has already been adjusted.");
            }

            foreach ($sale->saleItems as $saleItem) {
                $item = Item::WhereKey($saleItem->item_id)->lockForUpdate()->first();
                if (!$item) {
                    throw new \RuntimeException("Item not found (ID: {$saleItem->item_id}, SKU: {$saleItem->item->sku})");
                }

                $oldQty = (int) $item->quantity;
                $decrease = (int) $saleItem->quantity;
                if ($oldQty < $decrease) {
                    throw new \RuntimeException("Insufficient stock for {$item->name}. Available: {$oldQty}, required: {$decrease}.");
                }

                $newQty = $oldQty - $decrease;
                $item->update([
                    'quantity'      => $newQty,
                    'updated_by'    => $loggedUser->id,
                ]);
                StockHistory::create([
                    'item_id'       => $item->id,
                    'change'        => -$decrease,
                    'old_quantity'  => $oldQty,
                    'new_quantity'  => $newQty,
                    'reason'        => "Sale #{$sale->invoice_number}",
                    'created_by'    => $loggedUser->id,
                    'updated_by'    => $loggedUser->id,
                ]);
            }

            $totalPaid = $sale->payments()->where('status', PaymentStatus::SUCCESS->value)->sum('amount');
            if ($totalPaid >= $sale->total_amount) {
                $sale->update([
                    'is_paid'       => true,
                    'updated_by'    => $loggedUser->id,
                ]);
            }
        });

        return redirect()->route('sales.index')
            ->with('success', "Payment for Invoice: #{$sale->invoice_number} has been recorded and stock updated.");
    }
}
