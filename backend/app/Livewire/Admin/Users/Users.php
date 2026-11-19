<?php

namespace App\Livewire\Admin\Users;

use App\Livewire\Admin\Concerns\ConfirmsDeletion;
use App\Livewire\Admin\Concerns\HasAbilities;
use App\Livewire\Admin\Concerns\ManagesFormModal;
use App\Livewire\Admin\Concerns\Toasts;
use App\Livewire\Admin\Users\Forms\UserForm;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Hub;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/** İstifadəçilər (spec §9.3): # · Təfərrüatlar · Rol · İcazələr · Maaş · Status; filters name, email, phone. */
#[Title('İstifadəçilər')]
class Users extends Component
{
    use ConfirmsDeletion, HasAbilities, ManagesFormModal, Toasts, WithPagination;

    public UserForm $form;

    #[Url(except: '')]
    public string $name = '';

    #[Url(except: '')]
    public string $email = '';

    #[Url(except: '')]
    public string $phone = '';

    public function updated(string $property): void
    {
        if (in_array($property, ['name', 'email', 'phone'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset('name', 'email', 'phone');
        $this->resetPage();
    }

    protected function abilities(): array
    {
        return ['create' => 'user_create', 'update' => 'user_update', 'delete' => 'user_delete', 'permissions' => 'permission_update'];
    }

    protected function deletableQuery(): Builder
    {
        return User::query();
    }

    public function render()
    {
        return view('livewire.admin.users.users', [
            'users' => User::query()
                ->with('role:id,name,slug')
                ->when($this->name !== '', fn ($q) => $q->whereLike('name', "%{$this->name}%"))
                ->when($this->email !== '', fn ($q) => $q->whereLike('email', "%{$this->email}%"))
                ->when($this->phone !== '', fn ($q) => $q->whereLike('mobile', "%{$this->phone}%"))
                ->orderBy('name')
                ->paginate(10),
            'roles' => Role::query()->where('status', true)->orderBy('name')->get(['id', 'name', 'slug']),
            'designations' => Designation::query()->where('status', true)->orderBy('title')->get(['id', 'title']),
            'departments' => Department::query()->where('status', true)->orderBy('title')->get(['id', 'title']),
            'hubs' => Hub::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
