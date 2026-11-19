<?php

namespace App\Modules\Couriers\Enums;

/** Spec §7.2 bonus_type, used by salary generation (§8.5). The exact formula is open (§13.4). */
enum BonusType: string
{
    case Percent = 'percent';
    case PerParcel = 'per_parcel';

    public function label(): string
    {
        return match ($this) {
            self::Percent => 'Faizlə',
            self::PerParcel => 'Paket sayı məbləğ',
        };
    }
}
