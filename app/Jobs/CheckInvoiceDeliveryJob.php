<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\dashboard\Admin;
use App\Models\dashboard\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\InvoiceHaifTimePassed;
use Illuminate\Support\Facades\Notification;

class CheckInvoiceDeliveryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
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

            DB::table('notifications')
                ->whereIn('notifiable_id', $admin_tecks->pluck('id')->merge($admins->pluck('id')))
                ->where('notifiable_type', Admin::class)
                ->delete();

            Notification::send($admin_tecks, new InvoiceHaifTimePassed($invoices));
            Notification::send($admins, new InvoiceHaifTimePassed($invoices));
            Log::info('CheckInvoiceDeliveryJob is running at: ' . now());
        }
    }

}
