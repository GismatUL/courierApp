<?php

namespace App\Services;

use App\Enums\TariffType;
use App\Models\Zone;
use Illuminate\Support\Facades\DB;

/** Keeps a zone's tariff matrix (package type × tariff type) in sync with the edited grid. */
class ZoneTariffService
{
    /**
     * Rows are keyed by package type id, then tariff type value; a blank price removes that cell.
     *
     * @param  array<int|string, array<string, array{tracking_time?: ?string, delivery_time?: ?string, price?: ?string}>>  $rows
     */
    public function sync(Zone $zone, array $rows): int
    {
        return DB::transaction(function () use ($zone, $rows) {
            $kept = 0;

            foreach ($rows as $packageTypeId => $byTariff) {
                foreach ($byTariff as $tariff => $cell) {
                    $key = ['package_type_id' => (int) $packageTypeId, 'tariff_type' => TariffType::from($tariff)];

                    if (($cell['price'] ?? '') === '' || $cell['price'] === null) {
                        $zone->tariffs()->where($key)->delete();

                        continue;
                    }

                    $zone->tariffs()->updateOrCreate($key, [
                        'tracking_time' => ($cell['tracking_time'] ?? '') ?: null,
                        'delivery_time' => ($cell['delivery_time'] ?? '') ?: null,
                        'price' => $cell['price'],
                    ]);
                    $kept++;
                }
            }

            return $kept;
        });
    }
}
