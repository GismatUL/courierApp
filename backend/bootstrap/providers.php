<?php

use App\Modules\Access\AccessServiceProvider;
use App\Modules\Couriers\CouriersServiceProvider;
use App\Modules\Merchants\MerchantsServiceProvider;
use App\Modules\Network\NetworkServiceProvider;
use App\Modules\Parcels\ParcelsServiceProvider;
use App\Modules\Reports\ReportsServiceProvider;
use App\Modules\Settings\SettingsServiceProvider;
use App\Providers\AppServiceProvider;

// Module order is also the order of rows in the permission matrix.
return [
    AppServiceProvider::class,
    ReportsServiceProvider::class,
    AccessServiceProvider::class,
    MerchantsServiceProvider::class,
    NetworkServiceProvider::class,
    CouriersServiceProvider::class,
    ParcelsServiceProvider::class,
    SettingsServiceProvider::class,
];
