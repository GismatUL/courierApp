<?php

namespace App\Modules\Access\Livewire;

use App\Modules\Access\Livewire\Concerns\EditsPermissions;
use App\Modules\Access\Models\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

/**
 * Role create/edit with the permission matrix (spec §9.1 POST /admin/roles/store: name, status, permissions[]).
 * The slug is derived from the name on create and kept stable afterwards. Super Admin always has every
 * permission, so its matrix is read-only. Changing a role does not touch users who already have it:
 * their permissions are their own copy (spec §9.4).
 */
class RoleEditor extends Component
{
    use EditsPermissions;

    public ?Role $role = null;

    public string $name = '';

    public bool $status = true;

    public function mount(?Role $role = null): void
    {
        Gate::authorize($role?->exists ? 'role_update' : 'role_create');

        if ($role?->exists) {
            $this->role = $role;
            $this->name = $role->name;
            $this->status = $role->status;
            $this->permissions = $role->permissions;
        }
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('roles', 'name')->ignore($this->role?->id)],
            'status' => ['boolean'],
        ];
    }

    protected function validationAttributes(): array
    {
        return ['name' => 'Rol adı'];
    }

    public function save()
    {
        Gate::authorize($this->role ? 'role_update' : 'role_create');
        $this->validate();

        $isSuperAdmin = (bool) $this->role?->isSuperAdmin();
        $data = [
            'name' => $this->name,
            'status' => $isSuperAdmin ? true : $this->status,
            'permissions' => $isSuperAdmin ? [] : $this->grantablePermissions(),
        ];

        if ($this->role) {
            $this->role->update($data);
        } else {
            $slug = Str::slug($this->name) ?: 'role';
            $data['slug'] = Role::where('slug', $slug)->exists() ? $slug.'-'.Str::lower(Str::random(4)) : $slug;
            $this->role = Role::create($data);
        }

        session()->flash('toast', 'Rol yadda saxlanıldı.');

        return $this->redirectRoute('admin.roles.index', navigate: true);
    }

    public function render()
    {
        return view('access::role-editor')
            ->title($this->role ? "Rol: {$this->role->name}" : 'Yeni rol');
    }
}
