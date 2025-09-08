<?php

namespace App\Http\Controllers;

use App\Enums\SaleStatus;
use App\Models\Item;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Display a listing of the sales with filtering and sorting.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $sales = QueryBuilder::for(Sale::class)
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where('invoice_number', 'like', "%{$value}%")
                        ->orWhere('customer_name', 'like', "%{$value}%");
                }),
                AllowedFilter::callback('start_date', function ($query, $value) {
                    $query->whereDate('transaction_date', '>=', $value);
                }),
                AllowedFilter::callback('end_date', function ($query, $value) {
                    $query->whereDate('transaction_date', '<=', $value);
                }),
                AllowedFilter::exact('status'),
            ])
            ->allowedSorts([
                'customer_name',
                'total_amount',
                'status',
                'transaction_date',
            ])
            ->latest('transaction_date')
            ->paginate(10)
            ->withQueryString();

        return view('sales.index', compact('sales'));
    }

    /**
     * Show the form for creating a new sale.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $items = Item::where('is_active', true)->get();
        $invoice_number = Sale::generateInvoiceNumber();

        return view('sales.create', compact('items', 'invoice_number'));
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_number'   => 'required|string|unique:sales,invoice_number',
            'customer_name'    => 'required|string|max:255',
            'saleItems'        => 'required|array|min:1',
            'saleItems.*.id'   => 'required|exists:items,id',
            'saleItems.*.qty'  => 'required|integer|min:1',
        ]);

        $loggedUser = Auth::user();

        DB::transaction(function () use ($request, $loggedUser) {
            $sale = Sale::create([
                'invoice_number'    => $request->invoice_number,
                'customer_name'     => $request->customer_name,
                'total_amount'      => 0,
                'status'            => SaleStatus::UNPAID,
                'transaction_date'  => now(),
                'created_by'        => $loggedUser->id,
                'updated_by'        => $loggedUser->id,
            ]);

            $total = 0;

            foreach ($request->saleItems as $data) {
                $item = Item::findOrFail($data['id']);

                if ($data['qty'] > $item->quantity) {
                    return redirect()->back()->with('warning', "Insufficient stock for {$item->name} (available: {$item->quantity}).");
                }

                $subtotal = $item->price * $data['qty'];

                $sale->saleItems()->create([
                    'item_id'       => $item->id,
                    'quantity'      => $data['qty'],
                    'price'         => $item->price,
                    'subtotal'      => $subtotal,
                    'created_by'    => $loggedUser->id,
                    'updated_by'    => $loggedUser->id,
                ]);

                $total += $subtotal;
            }

            $sale->update(['total_amount' => $total]);
        });

        return redirect()->route('sales.index')->with('success', 'Sale created successfully (waiting for payment).');
    }

    /**
     * Show the form for editing the specified item.
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function edit(string $id)
    {
        $sale = Sale::with(['saleItems.item'])->findOrFail($id);
        if (!in_array($sale->status, [SaleStatus::UNPAID, SaleStatus::PARTIALLY_PAID])) {
            return redirect()->route('sales.index')->with('error', 'Only UNPAID or PARTIALLY PAID sales can be edited.');
        }

        $items = Item::where('is_active', true)->get();

        return view('sales.edit', compact('sale', 'items'));
    }


    /**
     * Update the specified sale in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'customer_name'    => 'required|string|max:255',
            'saleItems'        => 'required|array|min:1',
            'saleItems.*.id'   => 'required|exists:items,id',
            'saleItems.*.qty'  => 'required|integer|min:1',
        ]);

        $loggedUser = Auth::user();

        $sale = DB::transaction(function () use ($request, $id, $loggedUser) {
            $sale = Sale::with('saleItems')->findOrFail($id);

            $sale->update([
                'customer_name'  => $request->customer_name,
                'updated_by'     => $loggedUser->id,
            ]);

            $sale->saleItems()->delete();

            $total = 0;

            foreach ($request->saleItems as $data) {
                $item = Item::findOrFail($data['id']);

                if ($data['qty'] > $item->quantity) {
                    return redirect()->back()->with('warning', "Insufficient stock for {$item->name} (available: {$item->quantity}).");

                }

                $subtotal = $item->price * $data['qty'];

                $sale->saleItems()->create([
                    'item_id'    => $item->id,
                    'quantity'   => $data['qty'],
                    'price'      => $item->price,
                    'subtotal'   => $subtotal,
                    'created_by' => $loggedUser->id,
                    'updated_by' => $loggedUser->id,
                ]);

                $total += $subtotal;
            }

            $sale->update(['total_amount' => $total]);

            return $sale;
        });

        return redirect()->route('sales.index')->with('success', "Sale: {$sale->invoice_number} updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sale = Sale::findOrFail($id);
        $sale->delete();

        return redirect()
            ->route('sales.index')
            ->with('success', "{$sale->invoice_number} deleted successfully.");
    }
}
