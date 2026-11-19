<?php

namespace App\Modules\Access;

use App\Modules\Access\Console\CreateAdmin;
use App\Shared\Providers\ModuleServiceProvider;

/** Users, roles, permissions, login and profile (spec §2.4, §9). */
class AccessServiceProvider extends ModuleServiceProvider
{
    protected array $commands = [CreateAdmin::class];

    protected function module(): string
    {
        return 'Access';
    }
}
