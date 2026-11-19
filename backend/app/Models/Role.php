<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Permission template (spec §9.1). The "super-admin" role always has every permission. */
#[Fillable(['name', 'slug', 'permissions', 'status'])]
class Role extends Model
{
    public const SUPER_ADMIN = 'super-admin';

    protected $attributes = ['status' => true, 'permissions' => '[]'];

    protected function casts(): array
    {
        return ['permissions' => 'array', 'status' => 'boolean'];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->slug === self::SUPER_ADMIN;
    }

    public function deletionBlocker(): ?string
    {
        return match (true) {
            $this->isSuperAdmin() => 'Super Admin rolu silinə bilməz.',
            $this->users()->exists() => 'Bu rolda istifadəçilər var. Əvəzinə Passiv edin.',
            default => null,
        };
    }
}
