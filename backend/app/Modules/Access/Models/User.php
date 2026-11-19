<?php

namespace App\Modules\Access\Models;

use App\Modules\Access\Database\Factories\UserFactory;
use App\Modules\Network\Models\Hub;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Admin panel user (spec §9.3). Access is the user's own permission set (spec §9.4); a user whose
 * role is Super Admin passes every check (see AppServiceProvider Gate::before).
 */
#[Fillable([
    'name', 'email', 'mobile', 'password', 'status', 'role_id', 'designation_id', 'department_id',
    'permissions', 'address', 'nid_number', 'joining_date', 'salary',
])]
#[Hidden(['password', 'remember_token'])]
#[UseFactory(UserFactory::class)]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** Mirrors the column defaults so new, unsaved models behave the same. */
    protected $attributes = ['status' => true, 'panel_access' => true, 'permissions' => '[]'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'joining_date' => 'date',
            'password' => 'hashed',
            'permissions' => 'array',
            'salary' => 'decimal:2',
            'status' => 'boolean',
            'panel_access' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /** Hubs whose data this user may see (spec §9.3 hub_id[]). */
    public function hubs(): BelongsToMany
    {
        return $this->belongsToMany(Hub::class);
    }

    public function isActive(): bool
    {
        return $this->status;
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->role?->isSuperAdmin();
    }

    public function hasPermission(string $key): bool
    {
        return $this->isSuperAdmin() || in_array($key, $this->permissions ?? [], true);
    }

    public function initials(): string
    {
        return collect(preg_split('/\s+/', trim($this->name)))
            ->filter()
            ->take(2)
            ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    }

    public function deletionBlocker(): ?string
    {
        if ($this->is(auth()->user())) {
            return 'Öz hesabınızı silə bilməzsiniz.';
        }

        if ($this->isSuperAdmin() && ! auth()->user()?->isSuperAdmin()) {
            return 'Super Admin hesabını yalnız Super Admin silə bilər.';
        }

        if ($this->isSuperAdmin() && self::query()->whereRelation('role', 'slug', Role::SUPER_ADMIN)->count() <= 1) {
            return 'Sonuncu Super Admin silinə bilməz.';
        }

        return null;
    }
}
