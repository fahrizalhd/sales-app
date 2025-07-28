<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Item;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Sale::withCount('saleItems');

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $sales = $query->latest()->get();

        return view('sales.index', compact('sales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $items = Item::all();
        $date = now()->format('Ymd');
        $count = Sale::whereDate('created_at', today())->count() + 1;
        $invoice = 'INV-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        return view('sales.create', compact('items', 'invoice'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|unique:sales,invoice_number',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $sale = Sale::create([
                'invoice_number' => $validated['invoice_number'],
                'sale_date' => now(),
                'total_price' => 0,
            ]);

            $total = 0;

            foreach ($validated['items'] as $input) {
                $item = Item::findOrFail($input['item_id']);
                $subtotal = $item->price * $input['qty'];
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'item_id' => $item->id,
                    'qty' => $input['qty'],
                    'price' => $item->price,
                    'subtotal' => $subtotal,
                ]);
                $total += $subtotal;
            }

            $sale->update(['total_price' => $total]);

            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Sale recorded successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save sale: ' . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $sale = Sale::with('saleItems.item')->findOrFail($id);
        return view('sales.show', compact('sale'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $sale = Sale::with('saleItems')->findOrFail($id);

            if ($sale->payment) {
                return back()->with('error', 'Cannot delete sale that has been paid.');
            }

            foreach ($sale->saleItems as $item) {
                $item->delete();
            }

            $sale->delete();

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Sale deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete sale: ' . $e->getMessage());
        }
    }
}
