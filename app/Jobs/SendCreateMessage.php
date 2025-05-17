<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCreateMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    protected $invoice;
    protected $message;
    public function __construct($invoice, $message)
    {
        $this->invoice = $invoice;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $new_phone = preg_replace('/^0/', '', $this->invoice->phone);
        $new_phone = '966' . $new_phone;

        $params = [
            'instanceid' => '138796',
            'token' => '3fc4ad69-3ea3-4307-923c-7080f7aa0d8e',
            'phone' => $new_phone,
            'body' => $this->message,
        ];
        $queryString = http_build_query($params);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.4whats.net/sendMessage/?" . $queryString,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            Log::error('Failed to send WhatsApp message due to connection error', [
                'phone' => $new_phone,
                'error' => $err,
            ]);
            $this->invoice->update(['message_send' => 0]);
        } else {
            $responseData = json_decode($response, true);
            if (isset($responseData['sent']) && $responseData['sent'] === true) {
                Log::info('WhatsApp message sent successfully for invoice', [
                    'phone' => $new_phone,
                    'response' => $responseData,
                ]);
                $this->invoice->update(['message_send' => 1]);
            } else {
                $errorMessage = $responseData['message'] ?? 'سبب غير معروف';
                Log::error('Failed to send WhatsApp message', [
                    'phone' => $new_phone,
                    'response' => $responseData,
                ]);
                $this->invoice->update(['message_send' => 0]);
            }
        }
    }
}
