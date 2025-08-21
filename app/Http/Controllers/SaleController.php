<?php

namespace App\Http\Controllers;

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
                    $query->where('invoice_number', 'like', "%{$value}%");
                })
            ])
            ->allowedSorts([
                'total_amount',
                'is_paid'
            ])
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
            'items'            => 'required|array|min:1',
            'items.*.id'       => 'required|exists:items,id',
            'items.*.qty'      => 'required|integer|min:1',
        ]);

        $loggedUser = Auth::user();

        try {
            DB::transaction(function () use ($request, $loggedUser) {
                $sale = Sale::create([
                    'invoice_number' => $request->invoice_number,
                    'customer_name'  => $request->customer_name,
                    'total_amount'   => 0,
                    'is_paid'        => false,
                    'created_by'     => $loggedUser->id,
                    'updated_by'     => $loggedUser->id,
                ]);

                $total = 0;

                foreach ($request->items as $data) {
                    $item = Item::findOrFail($data['id']);

                    if ($data['qty'] > $item->quantity) {
                        throw new \Exception("Insufficient stock for {$item->name} (available: {$item->quantity}).");
                    }

                    $subtotal = $item->price * $data['qty'];

                    $sale->items()->create([
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
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['items' => $e->getMessage()]);
        }
    }
}
