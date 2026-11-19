<?php

namespace App\Modules\Reports;

use App\Shared\Providers\ModuleServiceProvider;

/** Dashboard now; reports and the activity log later (spec §2.3, §10). */
class ReportsServiceProvider extends ModuleServiceProvider
{
    protected function module(): string
    {
        return 'Reports';
    }
}
