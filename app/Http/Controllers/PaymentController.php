<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\SaleStatus;
use App\Models\Item;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments with filters and sorting.
     *
     */
    public function index()
    {
        $payments = QueryBuilder::for(Payment::class)
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->whereHas('sale', function ($q) use ($value) {
                        $q->where('invoice_number', 'like', "%{$value}%")
                            ->orWhere('customer_name', 'like', "%{$value}%");
                    })
                        ->orWhere('method', 'like', "%{$value}%")
                        ->orWhere('status', 'like', "%{$value}%");
                }),
                AllowedFilter::callback('start_date', function ($query, $value) {
                    $query->whereDate('created_at', '>=', $value);
                }),
                AllowedFilter::callback('end_date', function ($query, $value) {
                    $query->whereDate('created_at', '<=', $value);
                }),
            ])
            ->allowedSorts([
                'amount',
                'method',
                'status',
                'created_at',
                'created_by',
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new payment for a given sale.
     *
     */
    public function create(string $id)
    {
        $sale = Sale::with('saleItems.item')->findOrFail($id);
        $saleStatus = $sale->status;
        if ($saleStatus === SaleStatus::CANCELLED || $saleStatus === SaleStatus::PAID) {
            return redirect()->route('sales.index')
                ->with('error', 'Only UNPAID or PARTIALLY PAID sales can be proceed to payment.');
        }

        return view('payments.create', compact('sale'));
    }

    /**
     * Store a newly created payment in storage and update sale/stock.
     *
     */
    public function store(Request $request, Sale $sale)
    {
        $request->validate([
            'method' => ['required', new Enum(PaymentMethod::class)],
            'amount' => 'required|numeric|min:0',
        ]);

        $loggedUser = Auth::user();

        DB::transaction(function () use ($request, $sale, $loggedUser) {
            $paymentStatus = $request->method === PaymentMethod::CASH->value
                ? PaymentStatus::SUCCESS->value
                : PaymentStatus::PENDING->value;

            Payment::create([
                'sale_id'       => $sale->id,
                'amount'        => $request->amount,
                'method'        => $request->method,
                'status'        => $paymentStatus,
                'created_by'    => $loggedUser->id,
                'updated_by'    => $loggedUser->id,
            ]);

            // Prevent duplicate stock adjustment
            $alreadyAdjusted = StockHistory::whereIn('item_id', $sale->saleItems()->pluck('item_id'))
                ->where('reason', "Sale #{$sale->invoice_number}")
                ->exists();

            if ($alreadyAdjusted) {
                return redirect()->back()->with('warning', 'Stock for this sale has already been adjusted.');
            }

            // Adjust stock for each item in the sale
            foreach ($sale->saleItems as $saleItem) {
                $item = Item::whereKey($saleItem->item_id)->lockForUpdate()->first();
                if (!$item) {
                    return redirect()->back()->with('warning', "Item not found (ID: {$saleItem->item_id}, SKU: {$saleItem->item->sku})");
                }

                $oldQty = (int) $item->quantity;
                $decrease = (int) $saleItem->quantity;
                if ($oldQty < $decrease) {
                    return redirect()->back()->with('warning', "Insufficient stock for {$item->name}. Available: {$oldQty}, required: {$decrease}.");
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

            // Update sale status depending on payment method
            if ($request->method === PaymentMethod::CASH->value) {
                $sale->update([
                    'status'     => SaleStatus::PAID->value,
                    'updated_by' => $loggedUser->id,
                ]);
            } elseif (in_array($request->method, [PaymentMethod::QRIS->value, PaymentMethod::DEBIT->value])) {
                $sale->update([
                    'status'     => SaleStatus::NEED_REVIEW->value,
                    'updated_by' => $loggedUser->id,
                ]);
            }
        });

        return redirect()->route('sales.index')
            ->with('success', "Payment for Invoice: #{$sale->invoice_number} has been recorded and stock updated.");
    }

    /**
     * Display the specified payment details.
     *
     */
    public function show(string $id)
    {
        $payment = Payment::with(['sale.saleItems.item', 'sale.createdBy'])->findOrFail($id);

        return view('payments.show', compact('payment'));
    }

    /**
     * Display a printable version of the specified payment.
     *
     */
    public function print(string $id)
    {
        $payment = Payment::with(['sale.saleItems.item', 'sale.createdBy'])->findOrFail($id);

        return view('payments.print', compact('payment'));
    }

    /**
     * Approve the specified payment and update sale status.
     *
     */
    public function approve($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->update(['status' => PaymentStatus::SUCCESS->value]);

        if ($payment->sale->status !== SaleStatus::PAID) {
            $payment->sale->update(['status' => SaleStatus::PAID->value]);
        }

        return redirect()->route('payments.index', $id)
            ->with('success', 'Payment approved successfully.');
    }

    /**
     * Reject the specified payment.
     *
     */
    public function reject($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->update(['status' => PaymentStatus::REJECTED->value]);

        return redirect()->route('payments.show', $id)
            ->with('error', 'Payment rejected.');
    }
}
