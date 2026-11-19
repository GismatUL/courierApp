<?php

namespace App\Modules\Parcels\Events;

use App\Modules\Parcels\Models\Parcel;
use App\Modules\Parcels\Models\ParcelEvent;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Fired after every workflow action and undo (spec §3). Other modules react to it:
 * Finance writes ledger rows for "delivered" and reverses them on undo, SMS goes to the chosen recipients.
 * $event->action is the action key ("delivered", "undo", …); for undo, $revertedEvent is the event undone.
 */
class ParcelStatusChanged
{
    use Dispatchable;

    public function __construct(
        public readonly Parcel $parcel,
        public readonly ParcelEvent $event,
        public readonly ?ParcelEvent $revertedEvent = null,
    ) {}
}
