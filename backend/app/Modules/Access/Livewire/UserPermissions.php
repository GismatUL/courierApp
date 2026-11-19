<?php

namespace App\Modules\Access\Livewire;

use App\Modules\Access\Livewire\Concerns\EditsPermissions;
use App\Modules\Access\Models\User;
use App\Shared\Livewire\Concerns\Toasts;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Fərdi icazələr (spec §9.4, /admin/users/permissions/{id}): the same matrix as roles, saved on the user.
 * Super Admins have every permission implicitly, and nobody edits their own permissions.
 */
class UserPermissions extends Component
{
    use EditsPermissions, Toasts;

    #[Locked]
    public int $userId;

    public function mount(User $user): void
    {
        $this->guard($user);
        $this->userId = $user->id;
        $this->permissions = $user->permissions ?? [];
    }

    private function guard(User $user): void
    {
        Gate::authorize('permission_update');
        abort_if($user->isSuperAdmin() || $user->is(auth()->user()), 403);
    }

    public function resetToRole(): void
    {
        $this->permissions = User::with('role')->findOrFail($this->userId)->role?->permissions ?? [];
    }

    public function save(): void
    {
        $user = User::findOrFail($this->userId);
        $this->guard($user);

        $user->update(['permissions' => $this->grantablePermissions()]);
        $this->toast('İcazələr yadda saxlanıldı.');
    }

    public function render()
    {
        $user = User::with('role')->findOrFail($this->userId);

        return view('access::user-permissions', ['user' => $user])->title("İcazələr: {$user->name}");
    }
}
