<?php

namespace App\Modules\Parcels\Services;

use App\Modules\Merchants\Models\MerchantDeliveryCharge;
use App\Modules\Network\Enums\TariffType;
use App\Modules\Network\Models\ZoneTariff;

/**
 * Delivery price for one parcel unit (spec §4.2, §12.2 GET /pricing/quote):
 * the merchant's own tariff (§6.2) first, otherwise the zone's tariff matrix (§7.3).
 * How a round trip changes the price is not known (spec §13.4), so it does not change it yet.
 */
class Pricing
{
    /** @return array{price: string, tracking_time: ?string, delivery_time: ?string, source: string}|null */
    public function quote(int $merchantId, int $zoneId, int $packageTypeId, TariffType|string $tariff, bool $roundTrip = false): ?array
    {
        $tariff = $tariff instanceof TariffType ? $tariff->value : $tariff;
        $key = ['zone_id' => $zoneId, 'package_type_id' => $packageTypeId, 'tariff_type' => $tariff];

        $row = MerchantDeliveryCharge::where('merchant_id', $merchantId)->where($key)->first();
        $source = 'merchant';

        if ($row === null) {
            $row = ZoneTariff::where($key)->first();
            $source = 'zone';
        }

        return $row === null ? null : [
            'price' => number_format((float) $row->price, 2, '.', ''),
            'tracking_time' => $row->tracking_time,
            'delivery_time' => $row->delivery_time,
            'source' => $source,
        ];
    }

    /** Total the courier collects: cash on delivery + unit price × parcel count (spec §4.2). */
    public static function total(string|float $cashCollection, string|float $unitPrice, int $count): string
    {
        return number_format(round((float) $cashCollection + (float) $unitPrice * $count, 2), 2, '.', '');
    }
}
