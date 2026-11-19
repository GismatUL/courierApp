<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Sub-area of a zone (spec §7.3, /admin/zone/region). */
#[Fillable(['zone_id', 'name', 'status'])]
class Region extends Model
{
    protected $attributes = ['status' => true];

    protected function casts(): array
    {
        return ['status' => 'boolean'];
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function deletionBlocker(): ?string
    {
        return null;
    }
}
