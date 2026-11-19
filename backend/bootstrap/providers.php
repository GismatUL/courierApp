<?php

use App\Modules\Access\AccessServiceProvider;
use App\Modules\Network\NetworkServiceProvider;
use App\Modules\Reports\ReportsServiceProvider;
use App\Modules\Settings\SettingsServiceProvider;
use App\Providers\AppServiceProvider;

// Module order is also the order of rows in the permission matrix.
return [
    AppServiceProvider::class,
    ReportsServiceProvider::class,
    AccessServiceProvider::class,
    NetworkServiceProvider::class,
    SettingsServiceProvider::class,
];
