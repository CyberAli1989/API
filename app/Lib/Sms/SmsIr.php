<?php

namespace App\Lib\Sms;

use Illuminate\Support\Facades\Http;

class SmsIr
{
    private $api_key;
    private $verifyTemplateId = 100000;
    private $verifyUrl = 'https://api.sms.ir/v1/send/verify';

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->api_key = env('SMS_API_KEY');
    }

    public function verifyCode($number, $code)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'text/plain',
            'x-api-key' => $this->api_key
        ])->post($this->verifyUrl, [
            'mobile' => $number,
            'templateId' => $this->verifyTemplateId,
            'parameters' => [
                [
                    'name' => 'CODE',
                    'value' => $code,
                ]
            ]
        ]);
        if ($response['status'])
            return $response;
        else
            return response()->json([
                'success' => false,
                'message' => $response
            ]);
    }
}
