<?php

namespace App\Modules\Couriers\Models;

use App\Modules\Access\Models\User;
use App\Modules\Couriers\Enums\BonusType;
use App\Modules\Couriers\Enums\CarType;
use App\Modules\Couriers\Enums\CourierType;
use App\Modules\Network\Models\Hub;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Kuryer (spec §7.2). Login, name, email, phone and Aktiv/Passiv live on the linked user account.
 * Balances are kept per channel and change only through CourierBalance (Finance and Parcels call it).
 */
#[Fillable([
    'hub_id', 'type', 'car_type', 'car_number', 'voen', 'min_delivery_parcel', 'min_pickup_parcel',
    'delivery_charge', 'pickup_charge', 'return_charge', 'bonus_type', 'salary', 'work_days',
    'address', 'latitude', 'longitude',
])]
class Courier extends Model
{
    protected $table = 'delivery_men';

    /** Uploaded documents: column => label. */
    public const FILES = ['image_file' => 'Şəkil', 'driving_license_file' => 'Sürücülük vəsiqəsi'];

    protected function casts(): array
    {
        return [
            'type' => CourierType::class,
            'car_type' => CarType::class,
            'bonus_type' => BonusType::class,
            'delivery_charge' => 'decimal:2',
            'pickup_charge' => 'decimal:2',
            'return_charge' => 'decimal:2',
            'salary' => 'decimal:2',
            'opening_balance' => 'decimal:2',
            'balance_cash' => 'decimal:2',
            'balance_pos' => 'decimal:2',
            'balance_card' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hub(): BelongsTo
    {
        return $this->belongsTo(Hub::class);
    }

    /** "Cari balans": everything the courier currently holds across the three channels. */
    public function currentBalance(): string
    {
        return number_format((float) $this->balance_cash + (float) $this->balance_pos + (float) $this->balance_card, 2, '.', '');
    }

    public function deletionBlocker(): ?string
    {
        return (float) $this->currentBalance() !== 0.0
            ? 'Kuryerin balansı sıfır deyil. Əvəzinə Passiv edin.'
            : null;
    }
}
