<?php

namespace App\Enums;

/** Service level of a delivery (spec §4.2 tariff_type). The same codes must be used by both systems (§13.1.4). */
enum TariffType: string
{
    case Urgent = 'urgent';
    case SameDay = 'same_day';
    case Regular = 'regular';
    case Express = 'express';

    public function label(): string
    {
        return match ($this) {
            self::Urgent => 'Təcili',
            self::SameDay => 'Gün ərzində',
            self::Regular => 'Regular',
            self::Express => 'Ekspress',
        };
    }
}
