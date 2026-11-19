<?php

namespace App\Modules\Network\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Parcel size class by weight, e.g. Zərf 0–0.5 kg (spec §7.4). A null max_weight means no upper limit. */
#[Fillable(['name', 'min_weight', 'max_weight', 'description', 'status'])]
class PackageType extends Model
{
    protected $attributes = ['status' => true, 'min_weight' => 0];

    protected function casts(): array
    {
        return ['min_weight' => 'decimal:2', 'max_weight' => 'decimal:2', 'status' => 'boolean'];
    }

    public function zoneTariffs(): HasMany
    {
        return $this->hasMany(ZoneTariff::class);
    }

    public function weightRange(): string
    {
        $min = rtrim(rtrim($this->min_weight, '0'), '.');

        return $this->max_weight === null ? "{$min}+ kq" : $min.'–'.rtrim(rtrim($this->max_weight, '0'), '.').' kq';
    }

    public function deletionBlocker(): ?string
    {
        return $this->zoneTariffs()->exists() ? 'Bu paket növü zona tariflərində istifadə olunur. Əvəzinə Passiv edin.' : null;
    }
}
