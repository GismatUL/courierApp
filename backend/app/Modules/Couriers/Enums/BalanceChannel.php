<?php

namespace App\Modules\Couriers\Enums;

/** Where a courier's collected money sits (spec §5.1 cash_pay / pos_pay / card_pay, §8.2 pay_type). */
enum BalanceChannel: string
{
    case Cash = 'cash';
    case Pos = 'pos';
    case Card = 'card';

    public function column(): string
    {
        return 'balance_'.$this->value;
    }

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Nağd',
            self::Pos => 'P/Terminal',
            self::Card => 'CardToCard',
        };
    }
}
