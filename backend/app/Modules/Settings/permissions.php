<?php

use App\Shared\Support\Permissions;

// Settings module rows (spec §9.1).
return [
    'settings' => ['label' => 'Tənzimləmələr', 'keys' => [
        'general_settings_read' => 'Oxumaq', 'general_settings_update' => 'Yeniləmək',
    ] + Permissions::crud('currency')],
];
