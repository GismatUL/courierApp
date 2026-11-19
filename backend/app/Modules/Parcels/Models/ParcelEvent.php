<?php

namespace App\Modules\Parcels\Models;

use App\Modules\Access\Models\User;
use App\Modules\Couriers\Models\Courier;
use App\Modules\Network\Models\Hub;
use App\Modules\Parcels\Enums\ParcelStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One entry of the parcel log (spec §4.4 parcel_events). Written only by ParcelWorkflow. */
class ParcelEvent extends Model
{
    public const UPDATED_AT = null;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => ParcelStatus::class,
            'from_status' => ParcelStatus::class,
            'before' => 'array',
            'meta' => 'array',
            'reverted' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'created_at' => 'datetime',
        ];
    }

    public function parcel(): BelongsTo
    {
        return $this->belongsTo(Parcel::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function deliveryMan(): BelongsTo
    {
        return $this->belongsTo(Courier::class, 'delivery_man_id');
    }

    public function hub(): BelongsTo
    {
        return $this->belongsTo(Hub::class);
    }
}
