<?php

namespace App\Modules\Network;

use App\Shared\Providers\ModuleServiceProvider;

/** Hubs, zones and their tariffs, regions, package types, shelves, return reasons (spec §7). */
class NetworkServiceProvider extends ModuleServiceProvider
{
    protected function module(): string
    {
        return 'Network';
    }
}
