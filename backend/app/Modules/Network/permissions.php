<?php

use App\Shared\Support\Permissions;

// Network module rows (spec §9.1). Package types and return reasons have no keys of their own in the spec; they use the zone keys.
return [
    'hub' => ['label' => 'Filial', 'keys' => Permissions::crud('hub', ['read', 'create', 'update', 'delete', 'view'])],
    'hub_incharge' => ['label' => 'Filial məsulu', 'keys' => Permissions::crud('hub_incharge', ['read', 'create', 'update', 'delete', 'assigned'])],
    'zone' => ['label' => 'Zona, region, paket növü, qaytarma səbəbi', 'keys' => [
        'zone' => 'Oxumaq', 'zone_create' => 'Yaratmaq', 'zone_edit' => 'Redaktə', 'zone_delete' => 'Silmək',
        'region' => 'Regionlar: oxumaq', 'region_create' => 'Regionlar: yaratmaq', 'region_edit' => 'Regionlar: redaktə', 'region_delete' => 'Regionlar: silmək',
    ]],
];
