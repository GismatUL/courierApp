<?php

namespace App\Shared\Sms;

use Illuminate\Support\Facades\Log;

/** Development / placeholder gateway: records the SMS in the log instead of sending it. */
class LogSmsGateway implements SmsGateway
{
    public function send(string $phone, string $message): void
    {
        Log::info('SMS', ['to' => substr($phone, 0, -4).'****', 'message' => $message]);
    }
}
