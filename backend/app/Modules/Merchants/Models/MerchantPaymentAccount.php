<?php

namespace App\Modules\Merchants\Models;

use App\Modules\Merchants\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Where a merchant is paid out (spec §6.4): method + method-specific details. */
#[Fillable(['merchant_id', 'method', 'details', 'status'])]
class MerchantPaymentAccount extends Model
{
    protected $attributes = ['status' => true];

    protected function casts(): array
    {
        return ['method' => PaymentMethod::class, 'details' => 'array', 'status' => 'boolean'];
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /** One-line summary for the list, e.g. "Kapital Bank · Əli Məmmədov · AZ21NABZ…". */
    public function summary(): string
    {
        return collect($this->method->fields())
            ->keys()
            ->map(fn (string $key) => $this->details[$key] ?? null)
            ->filter()
            ->implode(' · ') ?: '—';
    }

    public function deletionBlocker(): ?string
    {
        return null;
    }
}
