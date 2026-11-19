<?php

namespace App\Modules\Parcels\Listeners;

use App\Modules\Couriers\Models\Courier;
use App\Modules\Parcels\Events\ParcelStatusChanged;
use App\Shared\Sms\SmsGateway;

/**
 * Spec §3: when the SMS boxes are ticked, the courier / merchant / customer is notified.
 * The real message texts are not known (spec §13.4); these are neutral placeholders.
 */
class SendParcelSms
{
    public function __construct(private readonly SmsGateway $sms) {}

    public function handle(ParcelStatusChanged $changed): void
    {
        $parcel = $changed->parcel;
        $targets = $changed->event->meta['sms'] ?? [];
        $text = "Kuryer.net: {$parcel->tracking_id} — {$parcel->status->label()}";

        $phones = [
            'customer' => $parcel->customer_phone,
            'merchant' => $parcel->merchant?->mobile,
            'courier' => Courier::with('user')->find($changed->event->delivery_man_id)?->user?->mobile,
        ];

        foreach ($targets as $target) {
            if (! empty($phones[$target])) {
                $this->sms->send($phones[$target], $text);
            }
        }
    }
}
