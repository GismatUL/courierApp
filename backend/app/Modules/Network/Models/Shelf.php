<?php

namespace App\Modules\Network\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Warehouse box/shelf that groups parcels of one zone for a courier route (spec §7.6). */
#[Fillable(['name', 'code', 'zone_id', 'limit', 'status'])]
class Shelf extends Model
{
    protected $attributes = ['status' => true];

    protected function casts(): array
    {
        return ['limit' => 'integer', 'status' => 'boolean'];
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function deletionBlocker(): ?string
    {
        return null; // Parcels are linked to shelves once the parcel module exists.
    }
}
