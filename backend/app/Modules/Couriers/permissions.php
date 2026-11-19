<?php

use App\Shared\Support\Permissions;

// Couriers module rows (spec §9.1).
return [
    'delivery_man' => ['label' => 'Kuryer', 'keys' => Permissions::crud('delivery_man')],
];
