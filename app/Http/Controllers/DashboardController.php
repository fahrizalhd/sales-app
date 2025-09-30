<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
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
     * Retrieves and calculates financial metrics for the current and previous month,
     * including earned and unearned revenue, top-selling items, payment channel usage,
     * and daily sales counts. Also handles role-based access control and date selection.
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

        $today = now();
        $weekIndex = $today->weekOfMonth;

        $selectedDate = Carbon::create($selectedYear, $selectedMonth, 1);
        $lastMonth = $selectedDate->copy()->subMonth();

        $targetDate = $selectedDate->copy()->addWeeks($weekIndex - 1);
        if ($targetDate->month != $selectedMonth) {
            $targetDate = $selectedDate->copy()->endOfMonth();
        }

        $thisWeekStart = $targetDate->copy()->startOfWeek(Carbon::MONDAY);
        $thisWeekEnd   = $targetDate->copy()->endOfWeek(Carbon::SUNDAY);
        if ($thisWeekStart->month != $selectedMonth) {
            $thisWeekStart = $selectedDate->copy()->startOfMonth();
        }
        if ($thisWeekEnd->month != $selectedMonth) {
            $thisWeekEnd = $selectedDate->copy()->endOfMonth();
        }

        $thisMonthStart = $selectedDate->copy()->startOfMonth();
        $thisMonthEnd   = $selectedDate->copy()->endOfMonth();
        $lastMonthStart = $lastMonth->copy()->startOfMonth();
        $lastMonthEnd   = $lastMonth->copy()->endOfMonth();

        $thisWeekRange  = $thisWeekStart->format('d F Y') . ' - ' . $thisWeekEnd->format('d F Y');
        $thisMonthName  = $selectedDate->format('F Y');
        $lastMonthName  = $lastMonth->format('F Y');

        $thisWeekRevenue = Sale::whereBetween('transaction_date', [$thisWeekStart, $thisWeekEnd])
            ->sum('total_amount');

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

        $topItems = SaleItem::select('item_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as revenue_per_item'))
            ->whereHas('sale', function ($query) use ($selectedMonth, $selectedYear) {
                $query->whereMonth('transaction_date', $selectedMonth)
                    ->whereYear('transaction_date', $selectedYear);
            })
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->with('item:id,name')
            ->take(5)
            ->get();

        $thisMonthSaleStatuses = Sale::selectRaw('status, COUNT(*) as count, SUM(total_amount) as revenue')
            ->whereBetween('transaction_date', [$thisMonthStart, $thisMonthEnd])
            ->groupBy('status')
            ->get()
            ->map(fn($row) => [
                'status' => $row->status->value,
                'count' => $row->count,
                'revenue' => $row->revenue,
                'label' => $row->status->label(),
            ]);

        $paymentChannels = Payment::where('status', PaymentStatus::SUCCESS)
            ->whereHas('sale', function ($q) use ($selectedMonth, $selectedYear) {
                $q->whereMonth('transaction_date', $selectedMonth)
                    ->whereYear('transaction_date', $selectedYear);
            })
            ->select('method', DB::raw('COUNT(*) as total'))
            ->groupBy('method')
            ->pluck('total', 'method')
            ->mapWithKeys(fn($total, $method) => [
                PaymentMethod::from($method)->label() => $total
            ]);

        $thisMonthSales = Sale::selectRaw("$dayExpr as day, COUNT(*) as total")
            ->whereBetween('transaction_date', [$thisMonthStart, $thisMonthEnd])
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $lastMonthSales = Sale::selectRaw("$dayExpr as day, COUNT(*) as total")
            ->whereBetween('transaction_date', [$lastMonthStart, $lastMonthEnd])
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            ->toArray();

        // $thisMonthSalesRaw = Sale::selectRaw("$dayExpr as day, COUNT(*) as total")
        //     ->whereBetween('transaction_date', [$thisMonthStart, $thisMonthEnd])
        //     ->groupBy('day')
        //     ->pluck('total', 'day');

        // $lastMonthSalesRaw = Sale::selectRaw("$dayExpr as day, COUNT(*) as total")
        //     ->whereBetween('transaction_date', [$lastMonthStart, $lastMonthEnd])
        //     ->groupBy('day')
        //     ->pluck('total', 'day');

        // $thisMonthSales = [];
        // for ($i = 1; $i <= $thisMonthStart->daysInMonth; $i++) {
        //     $thisMonthSales[$i] = $thisMonthSalesRaw[$i] ?? 0;
        // }

        // $lastMonthSales = [];
        // for ($i = 1; $i <= $lastMonthStart->daysInMonth; $i++) {
        //     $lastMonthSales[$i] = $lastMonthSalesRaw[$i] ?? 0;
        // }

        $latestSales = Sale::whereBetween('transaction_date', [$thisMonthStart, $thisMonthEnd])
            ->latest('transaction_date')
            ->take(10)
            ->get();

        return view('dashboard', [
            'thisWeekRevenue'              => $thisWeekRevenue,
            'thisMonthRevenue'             => $thisMonthRevenue,
            'lastMonthRevenue'             => $lastMonthRevenue,
            'thisWeekUnearnedRevenue'      => $thisWeekUnearnedRevenue,
            'thisMonthUnearnedRevenue'     => $thisMonthUnearnedRevenue,
            'lastMonthUnearnedRevenue'     => $lastMonthUnearnedRevenue,
            'topItems'                     => $topItems,
            'paidRevenue'                  => $paidRevenue,
            'unpaidRevenue'                => $unpaidRevenue,
            'thisMonthSaleStatuses'        => $thisMonthSaleStatuses,
            'paymentChannels'              => $paymentChannels,
            'thisMonthSales'               => $thisMonthSales,
            'lastMonthSales'               => $lastMonthSales,
            'latestSales'                  => $latestSales,
            'thisWeekRange'                => $thisWeekRange,
            'thisMonthName'                => $thisMonthName,
            'lastMonthName'                => $lastMonthName,
            'selectedMonth'                => $selectedMonth,
            'selectedYear'                 => $selectedYear,
        ]);
    }
}
