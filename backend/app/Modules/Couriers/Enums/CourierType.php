<?php

namespace App\Modules\Couriers\Enums;

/** Spec §7.2 deliveryman_type. */
enum CourierType: string
{
    case Company = 'company';
    case Remote = 'remote';

    public function label(): string
    {
        return match ($this) {
            self::Company => 'Şirkət İşçisi',
            self::Remote => 'Uzaqdan İşçi',
        };
    }
}
