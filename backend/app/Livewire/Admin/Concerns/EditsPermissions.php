<?php

namespace App\Livewire\Admin\Concerns;

use App\Support\Permissions;
use Illuminate\Validation\ValidationException;

/**
 * Permission matrix state (spec §9.1 "Modullar | İcazələr"), used by role and user permission forms.
 * Anyone other than a Super Admin may only grant keys they hold themselves (no privilege escalation).
 */
trait EditsPermissions
{
    /** @var list<string> */
    public array $permissions = [];

    public function toggleModule(string $module): void
    {
        $keys = array_keys(Permissions::modules()[$module]['keys'] ?? []);
        $allOn = array_diff($keys, $this->permissions) === [];

        $this->permissions = $allOn
            ? array_values(array_diff($this->permissions, $keys))
            : Permissions::sanitize([...$this->permissions, ...$keys]);
    }

    /** @return list<string> */
    protected function grantablePermissions(string $errorKey = 'permissions'): array
    {
        $keys = Permissions::sanitize($this->permissions);
        $editor = auth()->user();

        if (! $editor->isSuperAdmin() && $extra = array_diff($keys, $editor->permissions ?? [])) {
            throw ValidationException::withMessages([
                $errorKey => 'Özünüzdə olmayan icazələri verə bilməzsiniz: '.implode(', ', $extra),
            ]);
        }

        return $keys;
    }
}
