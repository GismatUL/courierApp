<?php

namespace App\Modules\Network\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Delivery zone of a hub; its tariff matrix is the base price when a merchant has no own tariff (spec §7.3). */
#[Fillable(['name', 'hub_id', 'note', 'status'])]
class Zone extends Model
{
    protected $attributes = ['status' => true];

    protected function casts(): array
    {
        return ['status' => 'boolean'];
    }

    public function hub(): BelongsTo
    {
        return $this->belongsTo(Hub::class);
    }

    public function tariffs(): HasMany
    {
        return $this->hasMany(ZoneTariff::class);
    }

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }

    public function shelves(): HasMany
    {
        return $this->hasMany(Shelf::class);
    }

    public function deletionBlocker(): ?string
    {
        return match (true) {
            $this->regions()->exists() => 'Bu zonanın regionları var. Əvəzinə Passiv edin.',
            $this->shelves()->exists() => 'Bu zonaya bağlı qutular var. Əvəzinə Passiv edin.',
            default => null,
        };
    }
}
