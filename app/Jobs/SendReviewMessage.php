<?php

namespace App\Jobs;

use App\Models\dashboard\Invoice;
use App\Models\dashboard\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendReviewMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $invoice;
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {

            $message_temp = Message::where('message_type', 'التقيم')->value('template_text');

            $new_phone = preg_replace('/^0/', '', $this->invoice->phone);
            // إضافة رمز البلد +966
            $new_phone = '966' . $new_phone;

            //$new_phone = '201011642731';

            $message = str_replace(
                ['{name}'],
                [$this->invoice->name],
                $message_temp
            );
            // dd($message);

            // تعريف المتغير
            $params = array(
                'instanceid' => '138796',
                'token' => '3fc4ad69-3ea3-4307-923c-7080f7aa0d8e',
                'phone' => $new_phone,
                'body' => $message,
            );
            $queryString = http_build_query($params); // تحويل المصفوفة إلى سلسلة نصية
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.4whats.net/sendMessage/?" . $queryString, // إضافة سلسلة الاستعلام إلى عنوان URL
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            Log::info("رسالة التقييم أُرسلت بنجاح إلى " . $new_phone);
        } catch (\Exception $e) {
            Log::error("خطأ في ارسال رسالة التقييم: " . $e->getMessage());
        }
    }
}
