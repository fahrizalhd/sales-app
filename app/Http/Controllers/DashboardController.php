<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with sales statistics and recent sales data.
     *
     */
    public function index(Request $request)
    {
        $driver = DB::getDriverName();
        switch ($driver) {
            case 'mysql':
                $dayExpr = "DAY(transaction_date)";
                break;
            case 'pgsql':
                $dayExpr = "CAST(TO_CHAR(transaction_date, 'DD') AS INTEGER)";
                break;
            default:
                $dayExpr = "CAST(strftime('%d', transaction_date) AS INTEGER)";
                break;
        }
        
        $loggedUser = Auth::user();
        if ($loggedUser->role === UserRole::USER) {
            return redirect()->back()->with('error', 'You do not have permission to access this page.');
        }

        $selectedMonth = $request->input('month', now()->month);
        $selectedYear  = $request->input('year', now()->year);

        $now = Carbon::create($selectedYear, $selectedMonth, 1);
        $lastMonth = $now->copy()->subMonth();

        $thisWeekStart  = $now->copy()->startOfWeek(Carbon::MONDAY);
        $thisWeekEnd    = $now->copy()->endOfWeek(Carbon::SUNDAY);
        $thisMonthStart = $now->copy()->startOfMonth();
        $thisMonthEnd   = $now->copy()->endOfMonth();
        $lastMonthStart = $lastMonth->copy()->startOfMonth();
        $lastMonthEnd   = $lastMonth->copy()->endOfMonth();

        $thisWeekRange  = $thisWeekStart->format('d F Y') . ' - ' . $thisWeekEnd->format('d F Y');
        $thisMonthName  = $now->format('F Y');
        $lastMonthName  = $lastMonth->format('F Y');

        $thisWeekRevenue = Sale::whereBetween('transaction_date', [$thisWeekStart, $thisWeekEnd])->sum('total_amount');
        $thisWeekUnearnedRevenue = Sale::whereBetween('transaction_date', [$thisWeekStart, $thisWeekEnd])
            ->whereIn('status', Sale::unpaidStatuses())
            ->sum('total_amount');

        $thisMonthRevenue = Sale::whereBetween('transaction_date', [$thisMonthStart, $thisMonthEnd])->sum('total_amount');
        $thisMonthUnearnedRevenue = Sale::whereBetween('transaction_date', [$thisMonthStart, $thisMonthEnd])
            ->whereIn('status', Sale::unpaidStatuses())
            ->sum('total_amount');

        $lastMonthRevenue = Sale::whereBetween('transaction_date', [$lastMonthStart, $lastMonthEnd])->sum('total_amount');
        $lastMonthUnearnedRevenue = Sale::whereBetween('transaction_date', [$lastMonthStart, $lastMonthEnd])
            ->whereIn('status', Sale::unpaidStatuses())
            ->sum('total_amount');

        $paidRevenue = Sale::whereBetween('transaction_date', [$thisMonthStart, $thisMonthEnd])
            ->whereIn('status', Sale::paidStatuses())
            ->sum('total_amount');

        $unpaidRevenue = Sale::whereBetween('transaction_date', [$thisMonthStart, $thisMonthEnd])
            ->whereIn('status', Sale::unpaidStatuses())
            ->sum('total_amount');

        $paidCount = Sale::whereBetween('transaction_date', [$thisMonthStart, $thisMonthEnd])
            ->whereIn('status', Sale::paidStatuses())
            ->count();

        $unpaidCount = Sale::whereBetween('transaction_date', [$thisMonthStart, $thisMonthEnd])
            ->whereIn('status', Sale::unpaidStatuses())
            ->count();

        $paymentChannels = Payment::whereHas('sale', function ($q) use ($selectedMonth, $selectedYear) {
            $q->whereMonth('transaction_date', $selectedMonth)
                ->whereYear('transaction_date', $selectedYear);
        })
            ->select('method', DB::raw('COUNT(*) as total'))
            ->groupBy('method')
            ->pluck('total', 'method');

        $thisMonthSales = Sale::selectRaw("$dayExpr as day, COUNT(*) as total")
            ->whereBetween('transaction_date', [$thisMonthStart, $thisMonthEnd])
            ->groupBy('day')
            ->pluck('total', 'day');

        $lastMonthSales = Sale::selectRaw("$dayExpr as day, COUNT(*) as total")
            ->whereBetween('transaction_date', [$lastMonthStart, $lastMonthEnd])
            ->groupBy('day')
            ->pluck('total', 'day');

        $topItems = SaleItem::select('item_id', DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('sale', function ($query) use ($selectedMonth, $selectedYear) {
                $query->whereMonth('transaction_date', $selectedMonth)
                    ->whereYear('transaction_date', $selectedYear);
            })
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->with('item:id,name')
            ->take(5)
            ->get();

        $latestSales = Sale::whereBetween('transaction_date', [$thisMonthStart, $thisMonthEnd])
            ->latest('transaction_date')
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
            'paymentChannels'           => $paymentChannels,
            'thisMonthSales'            => $thisMonthSales,
            'lastMonthSales'            => $lastMonthSales,
            'latestSales'               => $latestSales,
            'thisWeekRange'             => $thisWeekRange,
            'thisMonthName'             => $thisMonthName,
            'lastMonthName'             => $lastMonthName,
            'selectedMonth'             => $selectedMonth,
            'selectedYear'              => $selectedYear,
        ]);
    }
}
