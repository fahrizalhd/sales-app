<?php

namespace App\Providers;

use App\Enums\PaymentStatus;
use App\Models\Item;
use App\Models\Payment;
use App\Models\Sale;
use App\Observers\PaymentObserver;
use App\Observers\SaleObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        Sale::observe(SaleObserver::class);
        Payment::observe(PaymentObserver::class);

        View::composer('*', function ($view) {
            if (Auth::check()) {
                $unpaidSalesCount = Sale::where('status', Sale::unpaidStatuses())
                    ->whereMonth('transaction_date', now()->month)
                    ->whereYear('transaction_date', now()->year)
                    ->count();
                $pendingPaymentCount = Payment::where('status', PaymentStatus::PENDING)
                    ->whereHas('sale', function ($q) {
                        $q->whereMonth('transaction_date', now()->month)
                            ->whereYear('transaction_date', now()->year);
                    })
                    ->count();
                $lowStockItemThresholdMin = config('filters.item.low_stock_threshold_min');
                $lowStockItemThresholdMax = config('filters.item.low_stock_threshold_max');
                $lowStockItemCount = Item::whereBetween('quantity', [$lowStockItemThresholdMin, $lowStockItemThresholdMax])->count();

                $view->with([
                    'unpaidSalesCount'      => $unpaidSalesCount,
                    'pendingPaymentCount'   => $pendingPaymentCount,
                    'lowStockItemCount'     => $lowStockItemCount,
                ]);
            }
        });

        // View::composer('*', function ($view) {
        //     if (Auth::check()) {
        //         $notifications = Auth::user()->notifications()->get();

        //         $groupedNotifications = $notifications->sortByDesc(function ($n) {
        //             return Carbon::parse($n->created_at);
        //         })
        //         ->groupBy(function ($n) {
        //             $createdAt = Carbon::parse($n->created_at);

        //             if ($createdAt->isToday()) {
        //                 return 'Today';
        //             } elseif ($createdAt->isYesterday()) {
        //                 return 'Yesterday';
        //             } elseif ($createdAt->greaterThanOrEqualTo(now()->subDays(7))) {
        //                 return 'Last 7 Days';
        //             } else {
        //                 return 'Older';
        //             }
        //         })
        //             ->sortBy(function ($_, $group) {
        //                 $priority = [
        //                     'Today' => 1,
        //                     'Yesterday' => 2,
        //                     'Last 7 Days' => 3,
        //                     'Older' => 4,
        //                 ];
        //                 return $priority[$group] ?? 99;
        //             });

        //         $view->with('groupedNotifications', $groupedNotifications);
        //     }
        // });

        View::composer('*', function ($view) {
            if (Auth::check()) {
                $notifications = Auth::user()->notifications()->get();
                $saleIds = $notifications->pluck('data.sale_id')->filter()->unique();
                $sales = Sale::whereIn('id', $saleIds)->get()->keyBy('id');
                $groupedNotifications = $notifications
                    ->sortByDesc(function ($n) use ($sales) {
                        $saleId = $n->data['sale_id'] ?? null;
                        $sale = $saleId ? $sales->get($saleId) : null;

                        return $sale ? Carbon::parse($sale->transaction_date) : Carbon::parse($n->created_at);
                    })
                    ->groupBy(function ($n) use ($sales) {
                        $saleId = $n->data['sale_id'] ?? null;
                        $sale = $saleId ? $sales->get($saleId) : null;

                        $date = $sale ? Carbon::parse($sale->transaction_date) : Carbon::parse($n->created_at);

                        if ($date->isToday()) {
                            return 'Today';
                        } elseif ($date->isYesterday()) {
                            return 'Yesterday';
                        } elseif ($date->greaterThanOrEqualTo(now()->subDays(7))) {
                            return 'Last 7 Days';
                        } else {
                            return 'Older';
                        }
                    })
                    ->sortBy(function ($_, $group) {
                        $priority = [
                            'Today' => 1,
                            'Yesterday' => 2,
                            'Last 7 Days' => 3,
                            'Older' => 4,
                        ];
                        return $priority[$group] ?? 99;
                    });

                $view->with('groupedNotifications', $groupedNotifications);
            }
        });
    }
}
