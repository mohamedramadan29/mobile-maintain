<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\dashboard\Admin;
use Illuminate\Console\Command;
use App\Models\dashboard\Invoice;
use Illuminate\Support\Facades\Log;
use App\Notifications\InvoiceHaifTimePassed;
use Illuminate\Support\Facades\Notification;

class CheckInvoiceDelivery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:check-delivery';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check invoices where half the delivery time has passed but the device is not received.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $invoices = Invoice::where('status', 'رف الاستلام')->get()->filter(function ($invoice) {
            $deliveryDateTime = Carbon::parse($invoice->date_delivery . ' ' . $invoice->time_delivery);
            $createdDateTime = Carbon::parse($invoice->created_at);
            // حساب نصف الوقت بدقة أكبر
            $halfTime = $createdDateTime->copy()->addMinutes($createdDateTime->diffInMinutes($deliveryDateTime) / 2);

            // الشرط الأول: تجاوز نصف الوقت
            $halfTimePassed = Carbon::now()->greaterThanOrEqualTo($halfTime);

            // الشرط الثاني: الوقت انتهى بالفعل
            $deliveryTimePassed = Carbon::now()->greaterThanOrEqualTo($deliveryDateTime);
            return $halfTimePassed || $deliveryTimePassed;
        });
        if ($invoices->isNotEmpty()) {
            $admin_tecks = Admin::where('type', 'فني')->where('status', 1)->get();
            $admins = Admin::where('type', 'admin')->where('status', 1)->get();
            // إرسال الإشعارات للفنيين والمسؤولين
            Notification::send($admin_tecks, new InvoiceHaifTimePassed($invoices));
            Notification::send($admins, new InvoiceHaifTimePassed($invoices));
            Log::info('CheckInvoiceDeliveryJob is running at: ' . now());
        }
    }
}
