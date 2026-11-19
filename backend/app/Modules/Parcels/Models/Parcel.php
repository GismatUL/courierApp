<?php

namespace App\Modules\Parcels\Models;

use App\Modules\Access\Models\User;
use App\Modules\Couriers\Models\Courier;
use App\Modules\Merchants\Models\Merchant;
use App\Modules\Merchants\Models\MerchantShop;
use App\Modules\Network\Enums\TariffType;
use App\Modules\Network\Models\Hub;
use App\Modules\Network\Models\PackageType;
use App\Modules\Network\Models\RejectReason;
use App\Modules\Network\Models\Shelf;
use App\Modules\Network\Models\Zone;
use App\Modules\Parcels\Enums\ParcelStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Paket (spec §4). Status, assignees, shelf and money fields change only through ParcelWorkflow
 * (each change is logged as a ParcelEvent); create/edit go through ParcelService.
 */
#[Fillable([
    'merchant_invoice_no', 'merchant_id', 'merchant_shop_id', 'pickupman_id', 'zone_id', 'package_type_id',
    'tariff_type', 'customer_name', 'customer_phone', 'customer_address', 'latitude', 'longitude',
    'pickup_address', 'pickup_point', 'parcel_count', 'weight', 'fragile_liquid', 'has_round_trip',
    'priority', 'cash_collection', 'note', 'created_date',
])]
class Parcel extends Model
{
    protected $attributes = ['status' => 1, 'priority' => 'normal', 'parcel_count' => 1];

    protected function casts(): array
    {
        return [
            'status' => ParcelStatus::class,
            'tariff_type' => TariffType::class,
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'weight' => 'decimal:2',
            'cash_collection' => 'decimal:2',
            'delivery_charge' => 'decimal:2',
            'cod_charge' => 'decimal:2',
            'vat' => 'decimal:2',
            'total' => 'decimal:2',
            'fragile_liquid' => 'boolean',
            'has_round_trip' => 'boolean',
            'created_date' => 'datetime',
            'status_updated_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(MerchantShop::class, 'merchant_shop_id');
    }

    public function hub(): BelongsTo
    {
        return $this->belongsTo(Hub::class);
    }

    public function pickupman(): BelongsTo
    {
        return $this->belongsTo(Courier::class, 'pickupman_id');
    }

    public function deliveryMan(): BelongsTo
    {
        return $this->belongsTo(Courier::class, 'delivery_man_id');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function packageType(): BelongsTo
    {
        return $this->belongsTo(PackageType::class);
    }

    public function rejectReason(): BelongsTo
    {
        return $this->belongsTo(RejectReason::class);
    }

    public function shelf(): BelongsTo
    {
        return $this->belongsTo(Shelf::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function events(): HasMany
    {
        return $this->hasMany(ParcelEvent::class)->orderByDesc('id');
    }

    public function proof(): HasOne
    {
        return $this->hasOne(ParcelProof::class)->latestOfMany();
    }

    /** Money the courier must collect: cash on delivery plus the delivery fee (spec §4.2 "Ümumi Qiymət"). */
    public function amountToCollect(): string
    {
        return number_format((float) $this->total, 2, '.', '');
    }

    public function deletionBlocker(): ?string
    {
        return $this->status === ParcelStatus::Pending ? null : 'Yalnız "Gözləmədə" statusundakı paket silinə bilər.';
    }
}
