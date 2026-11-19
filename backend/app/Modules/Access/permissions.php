<?php

use App\Shared\Support\Permissions;

// Access module rows (spec §9.1).
return [
    'role' => ['label' => 'Rollar', 'keys' => Permissions::crud('role')],
    'designation' => ['label' => 'Vəzifələr', 'keys' => Permissions::crud('designation')],
    'department' => ['label' => 'Şöbələr', 'keys' => Permissions::crud('department')],
    'user' => ['label' => 'İstifadəçilər', 'keys' => Permissions::crud('user') + ['permission_update' => 'İcazələri dəyişmək']],
];
