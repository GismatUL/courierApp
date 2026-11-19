<?php

namespace App\Modules\Settings\Database\Seeders;

use App\Modules\Settings\Models\Currency;
use Illuminate\Database\Seeder;

/**
 * Only AZN is used (spec §10.4); the old system's 142 template currencies are not copied.
 * AZN is pegged at 1.70 per USD. Idempotent by code.
 */
class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        Currency::firstOrCreate(
            ['code' => 'AZN'],
            ['name' => 'Azərbaycan manatı', 'symbol' => '₼', 'exchange_rate' => 1.70, 'position' => 'after'],
        );
    }
}
