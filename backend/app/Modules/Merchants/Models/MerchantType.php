<?php

namespace App\Modules\Merchants\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Satıcı tipi (spec §6.6, /admin/merchant-type). */
#[Fillable(['title'])]
class MerchantType extends Model
{
    public function merchants(): HasMany
    {
        return $this->hasMany(Merchant::class);
    }

    public function deletionBlocker(): ?string
    {
        return null; // Merchants keep working without a type (the foreign key is set to null).
    }
}
