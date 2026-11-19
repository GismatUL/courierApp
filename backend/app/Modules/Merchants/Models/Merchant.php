<?php

namespace App\Modules\Merchants\Models;

use App\Modules\Network\Models\Hub;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Partnyor: a business that sends parcels (spec §6). Has its own tariffs, pickup points,
 * payment accounts and balance. Documents are private files served only through an authorised route.
 */
#[Fillable([
    'merchant_type_id', 'business_name', 'contact_name', 'email', 'mobile', 'password', 'address',
    'opening_balance', 'vat', 'return_charge_pct', 'cod_charge_inside_city', 'cod_charge_sub_city',
    'cod_charge_outside_city', 'payment_period_days', 'wallet_enabled', 'otp_required',
    'reference_name', 'reference_phone', 'status',
])]
#[Hidden(['password'])]
class Merchant extends Model
{
    /** Uploaded documents: column => label. */
    public const FILES = ['nid_file' => 'Şəxsiyyət vəsiqəsi', 'trade_license_file' => 'Müqavilə', 'image_file' => 'Şəkil / loqo'];

    protected $attributes = ['status' => true];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'opening_balance' => 'decimal:2',
            'current_balance' => 'decimal:2',
            'vat' => 'decimal:2',
            'return_charge_pct' => 'decimal:2',
            'cod_charge_inside_city' => 'decimal:2',
            'cod_charge_sub_city' => 'decimal:2',
            'cod_charge_outside_city' => 'decimal:2',
            'wallet_enabled' => 'boolean',
            'otp_required' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(MerchantType::class, 'merchant_type_id');
    }

    /** Hubs that serve this merchant (spec §6.1 hub[]). */
    public function hubs(): BelongsToMany
    {
        return $this->belongsToMany(Hub::class);
    }

    public function shops(): HasMany
    {
        return $this->hasMany(MerchantShop::class);
    }

    public function defaultShop(): HasOne
    {
        return $this->hasOne(MerchantShop::class)->where('is_default', true);
    }

    public function deliveryCharges(): HasMany
    {
        return $this->hasMany(MerchantDeliveryCharge::class);
    }

    public function paymentAccounts(): HasMany
    {
        return $this->hasMany(MerchantPaymentAccount::class);
    }

    /** Spec §6.6 edge case: a merchant with money on the balance (later also: active parcels) is deactivated, not deleted. */
    public function deletionBlocker(): ?string
    {
        return (float) $this->current_balance !== 0.0
            ? 'Partnyorun balansı sıfır deyil. Əvəzinə Passiv edin.'
            : null;
    }
}
