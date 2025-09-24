<?php

namespace App\Providers;

use App\Enums\PaymentStatus;
use App\Enums\SaleStatus;
use App\Models\Payment;
use App\Models\Sale;
use App\Observers\SaleObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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

        View::composer('*', function ($view) {
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
            $view->with([
                'unpaidSalesCount'      => $unpaidSalesCount,
                'pendingPaymentCount'   => $pendingPaymentCount,
            ]);
        });
    }
}
