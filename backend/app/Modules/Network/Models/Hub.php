<?php

namespace App\Modules\Network\Models;

use App\Modules\Access\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/** Branch / warehouse where parcels are received and couriers are based (spec §7.1). */
#[Fillable(['name', 'phone', 'address', 'latitude', 'longitude', 'contact_user_id', 'status'])]
class Hub extends Model
{
    protected $attributes = ['status' => true];

    protected function casts(): array
    {
        return ['latitude' => 'decimal:7', 'longitude' => 'decimal:7', 'status' => 'boolean'];
    }

    public function contactUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contact_user_id');
    }

    public function zones(): HasMany
    {
        return $this->hasMany(Zone::class);
    }

    public function deletionBlocker(): ?string
    {
        return match (true) {
            $this->zones()->exists() => 'Bu filiala bağlı zonalar var. Əvəzinə Passiv edin.',
            DB::table('hub_merchant')->where('hub_id', $this->id)->exists() => 'Bu filial partnyorlara xidmət edir. Əvəzinə Passiv edin.',
            default => null,
        };
    }
}
