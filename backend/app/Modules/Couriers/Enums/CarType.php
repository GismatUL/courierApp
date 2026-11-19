<?php

namespace App\Modules\Couriers\Enums;

/** Spec §7.2 deliveryman_car_type; also picks the courier's icon on the live map (spec §7.5). */
enum CarType: string
{
    case Car = 'car';
    case Motorbike = 'motorbike';
    case Van = 'van';
    case Bicycle = 'bicycle';
    case Truck = 'truck';

    public function label(): string
    {
        return match ($this) {
            self::Car => 'Minik',
            self::Motorbike => 'Motosiklet',
            self::Van => 'Furqon',
            self::Bicycle => 'Velosiped',
            self::Truck => 'Yük maşını',
        };
    }
}
