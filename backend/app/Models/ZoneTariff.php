<?php

namespace App\Models;

use App\Enums\TariffType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One price cell of a zone's matrix: package type × tariff type (unique per zone). */
#[Fillable(['zone_id', 'package_type_id', 'tariff_type', 'tracking_time', 'delivery_time', 'price'])]
class ZoneTariff extends Model
{
    protected function casts(): array
    {
        return ['tariff_type' => TariffType::class, 'price' => 'decimal:2'];
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function packageType(): BelongsTo
    {
        return $this->belongsTo(PackageType::class);
    }
}
