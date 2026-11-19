<?php

namespace App\Modules\Settings;

use App\Shared\Providers\ModuleServiceProvider;

/** General settings, Google Maps key, currencies (spec §10.2–10.4). */
class SettingsServiceProvider extends ModuleServiceProvider
{
    protected function module(): string
    {
        return 'Settings';
    }
}
