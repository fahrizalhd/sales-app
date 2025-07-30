<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get the start date for the 30-day period
        $startDate = now()->subDays(30)->startOfDay();

        $today = Carbon::today();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // 30-day sales record
        $salesRecord = Sale::selectRaw('DATE(sale_date) as date, SUM(total_price) as total')
            ->where('sale_date', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $salesLabels = $salesRecord->pluck('date');
        $salesData = $salesRecord->pluck('total');

        // Payment record
        $paidSalesCount = Sale::where('is_paid', true)
            ->where('sale_date', '>=', $startDate)
            ->count();

        $unpaidSalesCount = Sale::where('is_paid', false)
            ->where('sale_date', '>=', $startDate)
            ->count();

        // Top 5 sale items
        $topItems = DB::table('sales')
            ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->join('items', 'sale_items.item_id', '=', 'items.id')
            ->select('items.name', DB::raw('SUM(sale_items.qty) as total_sold'))
            ->where('sales.sale_date', '>=', $startDate)
            ->groupBy('items.id', 'items.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Today's sales and comparison with last month
        $todaySales = Sale::whereDate('sale_date', $today)->sum('total_price');

        $totalThisMonth = Sale::whereMonth('sale_date', $currentMonth)
            ->whereYear('sale_date', $currentYear)
            ->sum('total_price');

        $totalLastMonth = Sale::whereMonth('sale_date', Carbon::now()->subMonth()->month)
            ->whereYear('sale_date', Carbon::now()->subMonth()->year)
            ->sum('total_price');

        $comparison = $totalLastMonth > 0
            ? round((($totalThisMonth - $totalLastMonth) / $totalLastMonth) * 100, 1)
            : null;

        return view('dashboard.index', compact(
            'salesLabels',
            'salesData',
            'paidSalesCount',
            'unpaidSalesCount',
            'topItems', 
            'todaySales',
            'totalThisMonth',
            'totalLastMonth',
            'comparison'
        ));
    }
}
