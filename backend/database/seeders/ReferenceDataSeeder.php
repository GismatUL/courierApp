<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\PackageType;
use App\Models\RejectReason;
use Illuminate\Database\Seeder;

/**
 * Reference values that the spec lists exactly (§7.4 package types, §7.7 reject reasons, §10.4 AZN).
 * Idempotent: existing rows are matched by name and left as edited.
 */
class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        $packageTypes = [
            ['Zərf', 0, 0.5],
            ['Bağlama10', 0.5, 10],
            ['Bağlama25', 10, 25],
            ['Bağlama50', 25, 50],
            ['Bağlama50+', 50, null],
        ];

        foreach ($packageTypes as [$name, $min, $max]) {
            PackageType::firstOrCreate(['name' => $name], ['min_weight' => $min, 'max_weight' => $max]);
        }

        $reasons = [
            'Bu ünvanda fəaliyyət göstərmir',
            'Bu ünvanda yaşamır',
            'İmtina edildi',
            'Qapı bağlıdır',
            'Ünvan düzgün deyil',
            'Ünvan tam deyil',
        ];

        foreach ($reasons as $name) {
            RejectReason::firstOrCreate(['name' => $name]);
        }

        // Only AZN is used (spec §10.4); the old system's 142 template currencies are not copied. AZN is pegged at 1.70 per USD.
        Currency::firstOrCreate(
            ['code' => 'AZN'],
            ['name' => 'Azərbaycan manatı', 'symbol' => '₼', 'exchange_rate' => 1.70, 'position' => 'after'],
        );
    }
}
