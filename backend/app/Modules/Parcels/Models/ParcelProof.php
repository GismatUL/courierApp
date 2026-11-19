<?php

namespace App\Modules\Parcels\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Proof of delivery sent by the courier app (spec §4.5): photo, signature, payment split, location. */
class ParcelProof extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['cash_pay' => 'decimal:2', 'pos_pay' => 'decimal:2', 'card_pay' => 'decimal:2'];
    }

    public function parcel(): BelongsTo
    {
        return $this->belongsTo(Parcel::class);
    }
}
