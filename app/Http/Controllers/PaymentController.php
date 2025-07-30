<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{

    public function index(Request $request)
    {
        $query = Payment::with('sale');
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $payments = $query->latest()->get();
        return view('payments.index', compact('payments'));
    }

    public function create(Sale $sale)
    {
        if ($sale->payment) {
            return redirect()->route('sales.index')->with('error', 'Payment has already been made.');
        }

        return view('payments.create', compact('sale'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $sale = Sale::with('saleItems')->findOrFail($request->sale_id);

        if ($sale->payment) {
            return back()->with('error', 'Payment has already been created.');
        }

        DB::beginTransaction();
        try {
            Payment::create([
                'sale_id' => $sale->id,
                'payment_code' => 'PAY-' . now()->format('YmdHis'),
                'amount' => $request->amount,
            ]);

            $sale->update(['is_paid' => true]);

            foreach ($sale->saleItems as $saleItem) {
                $item = $saleItem->item;
                $item->decrement('qty', $saleItem->qty);
            }

            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Payment successful.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }
}
