<?php

namespace App\Modules\Parcels\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Müştəri: recipients collected automatically from parcels (spec §9.5); read-only in the panel. */
#[Fillable(['name', 'phone', 'address'])]
class Customer extends Model
{
    public function parcels(): HasMany
    {
        return $this->hasMany(Parcel::class);
    }
}
