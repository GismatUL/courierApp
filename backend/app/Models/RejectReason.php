<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/** Why a delivery failed, shown under the parcel status (spec §7.7). */
#[Fillable(['name', 'status'])]
class RejectReason extends Model
{
    protected $attributes = ['status' => true];

    protected function casts(): array
    {
        return ['status' => 'boolean'];
    }

    public function deletionBlocker(): ?string
    {
        return null; // Parcels reference reasons once the parcel module exists.
    }
}
