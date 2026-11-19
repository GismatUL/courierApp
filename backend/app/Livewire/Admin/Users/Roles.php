<?php

namespace App\Livewire\Admin\Users;

use App\Livewire\Admin\Concerns\ConfirmsDeletion;
use App\Livewire\Admin\Concerns\HasAbilities;
use App\Livewire\Admin\Concerns\Toasts;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Title;
use Livewire\Component;

/** Rollar (spec §9.1): # · Rol · Slug · İcazə sayı · Status. Editing happens on RoleEditor. */
#[Title('Rollar')]
class Roles extends Component
{
    use ConfirmsDeletion, HasAbilities, Toasts;

    protected function abilities(): array
    {
        return ['create' => 'role_create', 'update' => 'role_update', 'delete' => 'role_delete'];
    }

    protected function deletableQuery(): Builder
    {
        return Role::query();
    }

    public function render()
    {
        return view('livewire.admin.users.roles', [
            'roles' => Role::query()->withCount('users')->orderBy('id')->get(),
        ]);
    }
}
