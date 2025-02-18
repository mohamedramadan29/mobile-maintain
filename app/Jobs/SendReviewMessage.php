<?php

namespace App\Jobs;

use App\Models\dashboard\Invoice;
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

            // إنشاء رابط التقييم على Google Maps
            $google_map_review_link = "https://maps.app.goo.gl/gMLTFSnBZ9Shjs2q8?g_st=ic"; // ضع رابط التقييم الخاص بك

            // استخراج بيانات الفاتورة
            $new_phone = preg_replace('/^0/', '', $this->invoice->phone);
            $new_phone = '966' . $new_phone;

            // رسالة التقييم
            $message = "🌟 *مرحبًا استاذ " . $this->invoice->name . "* 🌟\n\n";
            $message .= "نود معرفة رأيك حول الخدمة التي قدمناها لك. 😃\n\n";
            $message .= "يمكنك تقييمنا عبر الرابط التالي:\n";
            $message .= "🔗 *رابط التقييم:* " . $google_map_review_link . "\n\n";
            $message .= "شكرًا لك على وقتك! 💙";

            // إرسال الرسالة عبر API واتساب
            $params = [
                'instanceid' => '138484',
                'token' => '573f5335-db32-422f-8a7f-efc7a18654f9',
                'phone' => $new_phone,
                'body' => $message,
            ];
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
