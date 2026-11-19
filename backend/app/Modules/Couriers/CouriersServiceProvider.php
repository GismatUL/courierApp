<?php

namespace App\Modules\Couriers;

use App\Shared\Providers\ModuleServiceProvider;

/** Couriers with app-only login accounts and per-channel balances (spec §7.2). */
class CouriersServiceProvider extends ModuleServiceProvider
{
    protected function module(): string
    {
        return 'Couriers';
    }
}
