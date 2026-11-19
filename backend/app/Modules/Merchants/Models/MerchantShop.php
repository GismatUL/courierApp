<?php

namespace App\Modules\Merchants\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Merchant pickup point ("Filiallar" tab, spec §6.3). One per merchant is the default ("Təyin edilmiş"). */
#[Fillable(['merchant_id', 'name', 'contact_no', 'address', 'latitude', 'longitude', 'is_default', 'status'])]
class MerchantShop extends Model
{
    protected $attributes = ['status' => true, 'is_default' => false];

    protected function casts(): array
    {
        return ['latitude' => 'decimal:7', 'longitude' => 'decimal:7', 'is_default' => 'boolean', 'status' => 'boolean'];
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function deletionBlocker(): ?string
    {
        return null;
    }
}
