<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Şöbə (spec §9.2): a simple title + status list assigned to users. */
#[Fillable(['title', 'status'])]
class Department extends Model
{
    protected $attributes = ['status' => true];

    protected function casts(): array
    {
        return ['status' => 'boolean'];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function deletionBlocker(): ?string
    {
        return null; // Users keep working without it (the foreign key is set to null).
    }
}
