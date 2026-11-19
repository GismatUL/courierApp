<?php

namespace App\Modules\Merchants\Models;

use App\Modules\Network\Enums\TariffType;
use App\Modules\Network\Models\PackageType;
use App\Modules\Network\Models\Zone;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Merchant's own price for zone × package type × tariff type (spec §6.2). Unique per merchant;
 * when a combination is missing, the zone's tariff matrix applies (spec §4.2).
 */
#[Fillable(['merchant_id', 'zone_id', 'package_type_id', 'tariff_type', 'tracking_time', 'delivery_time', 'price'])]
class MerchantDeliveryCharge extends Model
{
    protected function casts(): array
    {
        return ['tariff_type' => TariffType::class, 'price' => 'decimal:2'];
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function packageType(): BelongsTo
    {
        return $this->belongsTo(PackageType::class);
    }

    public function deletionBlocker(): ?string
    {
        return null;
    }
}
