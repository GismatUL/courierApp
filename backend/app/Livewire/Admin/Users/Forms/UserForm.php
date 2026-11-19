<?php

namespace App\Livewire\Admin\Users\Forms;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Form;

/**
 * Spec §9.3 POST /admin/users/store. When the role is set or changed, the user's permissions are
 * copied from the role template (spec §9.4). Only a Super Admin may grant or edit Super Admins,
 * and nobody can change their own role or deactivate themselves.
 */
class UserForm extends Form
{
    #[Locked]
    public ?int $id = null;

    public string $name = '';

    public string $email = '';

    public string $mobile = '';

    public string $password = '';

    public string $role_id = '';

    public string $designation_id = '';

    public string $department_id = '';

    public bool $status = true;

    public string $address = '';

    public string $nid_number = '';

    public string $joining_date = '';

    public string $salary = '';

    /** @var list<string> */
    public array $hub_ids = [];

    public function setRecord(?int $id): void
    {
        $this->reset();

        if ($id === null) {
            return;
        }

        $user = User::with('hubs:id')->findOrFail($id);
        $this->guardEditable($user);

        $this->id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->mobile = (string) $user->mobile;
        $this->role_id = (string) $user->role_id;
        $this->designation_id = (string) $user->designation_id;
        $this->department_id = (string) $user->department_id;
        $this->status = $user->status;
        $this->address = (string) $user->address;
        $this->nid_number = (string) $user->nid_number;
        $this->joining_date = (string) $user->joining_date?->format('Y-m-d');
        $this->salary = (float) $user->salary ? (string) (float) $user->salary : '';
        $this->hub_ids = $user->hubs->pluck('id')->map(fn ($id) => (string) $id)->all();
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->id)],
            'mobile' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9 ()-]{7,20}$/'],
            // bcrypt ignores input past 72 bytes, so longer passwords are refused rather than silently truncated.
            'password' => [$this->id ? 'nullable' : 'required', 'string', 'min:10', 'max:72'],
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')->where('status', true)],
            'designation_id' => ['nullable', 'integer', 'exists:designations,id'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'status' => ['boolean'],
            'address' => ['nullable', 'string', 'max:500'],
            'nid_number' => ['nullable', 'string', 'max:30'],
            'joining_date' => ['nullable', 'date'],
            'salary' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'hub_ids' => ['array'],
            'hub_ids.*' => ['integer', 'exists:hubs,id'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'name' => 'Ad', 'email' => 'Email', 'mobile' => 'Telefon', 'password' => 'Şifrə', 'role_id' => 'Rol',
            'designation_id' => 'Vəzifə', 'department_id' => 'Şöbə', 'address' => 'Ünvan', 'nid_number' => 'Şəxsiyyət vəsiqəsi',
            'joining_date' => 'Qoşulma tarixi', 'salary' => 'Maaş', 'hub_ids' => 'Filiallar',
        ];
    }

    public function save(): string
    {
        $data = $this->validate();
        $editor = auth()->user();
        $user = $this->id ? User::findOrFail($this->id) : new User;
        $role = Role::findOrFail($data['role_id']);

        if ($user->exists) {
            $this->guardEditable($user);
        }

        if ($role->isSuperAdmin() && ! $editor->isSuperAdmin()) {
            throw ValidationException::withMessages(['form.role_id' => 'Super Admin rolunu yalnız Super Admin verə bilər.']);
        }

        if ($user->is($editor) && ((int) $data['role_id'] !== $user->role_id || ! $data['status'])) {
            throw ValidationException::withMessages(['form.role_id' => 'Öz rolunuzu dəyişə və ya hesabınızı passiv edə bilməzsiniz.']);
        }

        $roleChanged = $user->role_id !== $role->id;
        $fields = collect($data)->except(['password', 'hub_ids'])->map(fn ($v) => $v === '' ? null : $v)->all();
        $fields['salary'] ??= 0;

        DB::transaction(function () use ($user, $fields, $data, $role, $roleChanged) {
            $user->fill($fields);

            if ($data['password'] !== '' && $data['password'] !== null) {
                $user->password = $data['password'];
            }

            if ($roleChanged) {
                $user->permissions = $role->permissions;
            }

            $user->save();
            $user->hubs()->sync(array_map('intval', $data['hub_ids']));
        });

        return $this->id ? 'İstifadəçi yeniləndi.' : 'İstifadəçi əlavə edildi.';
    }

    /** A Super Admin account can only be changed by another Super Admin. */
    private function guardEditable(User $user): void
    {
        if ($user->isSuperAdmin() && ! auth()->user()->isSuperAdmin()) {
            throw ValidationException::withMessages(['form.name' => 'Super Admin hesabını yalnız Super Admin dəyişə bilər.']);
        }
    }
}
