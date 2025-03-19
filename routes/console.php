<?php

use App\Console\Commands\CheckInvoiceDelivery;
use App\Jobs\CheckInvoiceDeliveryJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

//Schedule::job(new CheckInvoiceDeliveryJob)->everySecond();

Schedule::command(CheckInvoiceDelivery::class)->dailyAt('11:00');