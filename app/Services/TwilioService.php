<?php

namespace App\Services;

use Twilio\Rest\Client;

/**
 * Class TwilioService.
 */
class TwilioService
{
    protected $twilio;

    public function __construct()
    {
        $this->twilio = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
    }

    public function sendSms($to, $message)
    {
        return $this->twilio->messages->create($to, [
            'from' => env('TWILIO_FROM'),
            'body' => $message,
        ]);
    }
}
