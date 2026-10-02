<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * إرسال رسالة واتساب بحالة الجهاز.
 *
 * لا تنفّذ ShouldQueue عمدًا: تُرسَل عبر dispatchAfterResponse أي بعد وصول
 * الرد للمتصفح، فلا ينتظرها الفني ولا تحتاج إلى queue worker يعمل.
 */
class SendStatusMessage
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phone;
    protected $message;
    protected $invoiceId;

    public function __construct($phone, $message, $invoiceId = null)
    {
        $this->phone = $phone;
        $this->message = $message;
        $this->invoiceId = $invoiceId;
    }

    public function handle(): void
    {
        $params = [
            'instanceid' => '138796',
            'token' => '3fc4ad69-3ea3-4307-923c-7080f7aa0d8e',
            'phone' => $this->phone,
            'body' => $this->message,
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.4whats.net/sendMessage/?' . http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            Log::error('تعذر إرسال رسالة حالة الجهاز', [
                'invoice_id' => $this->invoiceId,
                'phone' => $this->phone,
                'error' => $err,
            ]);
            return;
        }

        $data = json_decode($response, true);
        if (!isset($data['sent']) || $data['sent'] !== true) {
            Log::warning('رُفض إرسال رسالة حالة الجهاز', [
                'invoice_id' => $this->invoiceId,
                'phone' => $this->phone,
                'response' => $data,
            ]);
        }
    }
}
