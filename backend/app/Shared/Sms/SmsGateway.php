<?php

namespace App\Shared\Sms;

/**
 * SMS sending. The provider is not known yet (spec §13.4), so the default binding (LogSmsGateway)
 * writes messages to the log; bind a real provider in AppServiceProvider when it is chosen.
 */
interface SmsGateway
{
    public function send(string $phone, string $message): void;
}
