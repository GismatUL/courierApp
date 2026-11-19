<?php

use App\Shared\Support\Permissions;

// Parcels module rows (spec §9.1).
return [
    'parcel' => ['label' => 'Paket', 'keys' => Permissions::crud('parcel') + ['parcel_status_update' => 'Status dəyişmək']],
];
