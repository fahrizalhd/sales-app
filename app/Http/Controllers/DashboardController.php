<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with sales statistics and recent sales data.
     *
     * @return \Illuminate\View\View Returns the dashboard view with all sales statistics.
     */
    public function index()
    {
        $loggedUser = Auth::user();
        if ($loggedUser->role === UserRole::USER) {
            return redirect()->back()->with('error', 'You do not have permission to access this page.');
        }
        
        $now = Carbon::now();

        $thisWeekRevenue = Sale::whereBetween('created_at', [
            $now->copy()->startOfWeek(Carbon::MONDAY),
            $now->copy()->endOfWeek(Carbon::SUNDAY),
        ])
            ->sum('total_amount');

        $thisMonthRevenue = Sale::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->sum('total_amount');

        $lastMonth = $now->copy()->subMonth();
        $lastMonthRevenue = Sale::whereYear('created_at', $lastMonth->year)
            ->whereMonth('created_at', $lastMonth->month)
            ->sum('total_amount');

        $thisWeekUnearnedRevenue = Sale::whereBetween('created_at', [
            $now->copy()->startOfWeek(Carbon::MONDAY),
            $now->copy()->endOfWeek(Carbon::SUNDAY),
        ])
            ->where('is_paid', false)
            ->sum('total_amount');

        $thisMonthUnearnedRevenue = Sale::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->where('is_paid', false)
            ->sum('total_amount');

        $lastMonth = $now->copy()->subMonth();
        $lastMonthUnearnedRevenue = Sale::whereYear('created_at', $lastMonth->year)
            ->whereMonth('created_at', $lastMonth->month)
            ->where('is_paid', false)
            ->sum('total_amount');

        $topItems = SaleItem::select('item_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->with('item:id,name')
            ->take(5)
            ->get();

        $paidRevenue = Sale::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->where('is_paid', true)
            ->sum('total_amount');

        $unpaidRevenue = Sale::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->where('is_paid', false)
            ->sum('total_amount');

        $paidCount = Sale::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->where('is_paid', true)
            ->count();

        $unpaidCount = Sale::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->where('is_paid', false)
            ->count();

        $thisMonthSales = Sale::selectRaw("strftime('%d', created_at) as day, COUNT(*) as total")
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->where('is_paid', true)
            ->groupBy('day')
            ->pluck('total', 'day');

        $lastMonthSales = Sale::selectRaw("strftime('%d', created_at) as day, COUNT(*) as total")
            ->whereMonth('created_at', $now->subMonth()->month)
            ->whereYear('created_at', $now->subMonth()->year)
            ->where('is_paid', true)
            ->groupBy('day')
            ->pluck('total', 'day');

        $latestSales = Sale::latest('created_at')
            ->take(10)
            ->get();

        return view('dashboard', [
            'thisWeekRevenue'           => $thisWeekRevenue,
            'thisMonthRevenue'          => $thisMonthRevenue,
            'lastMonthRevenue'          => $lastMonthRevenue,
            'thisWeekUnearnedRevenue'   => $thisWeekUnearnedRevenue,
            'thisMonthUnearnedRevenue'  => $thisMonthUnearnedRevenue,
            'lastMonthUnearnedRevenue'  => $lastMonthUnearnedRevenue,
            'topItems'                  => $topItems,
            'paidRevenue'               => $paidRevenue,
            'unpaidRevenue'             => $unpaidRevenue,
            'paidCount'                 => $paidCount,
            'unpaidCount'               => $unpaidCount,
            'thisMonthSales'            => $thisMonthSales,
            'lastMonthSales'            => $lastMonthSales,
            'latestSales'               => $latestSales,
        ]);
    }
}
