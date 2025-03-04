<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use App\Jobs\CheckInvoiceDeliveryJob;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schedule;

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
        // تشغيل الوظيفة فور تشغيل التطبيق (للاختبار فقط)
        // dispatch(new \App\Jobs\CheckInvoiceDeliveryJob);
        Paginator::useBootstrap();
        foreach (config('permissions') as $config_permission => $value) {
            Gate::define($config_permission, function ($auth) use ($config_permission) {
                return $auth->hasAccess($config_permission);
            });
        }
        // $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
        //     $schedule->job(new CheckInvoiceDeliveryJob)->everySecond();
        // });
    }
}
