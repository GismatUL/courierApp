<div class="flex flex-col gap-5">
    <x-admin.page-header description="Rol icazə şablonudur: istifadəçiyə rol verildikdə icazələr ondan köçürülür, sonra istifadəçi üzrə dəyişdirilə bilər.">
        @if ($this->can('create'))<x-admin.button :href="route('admin.roles.create')" wire:navigate><x-admin.icon name="plus" class="size-4" /> Əlavə et</x-admin.button>@endif
    </x-admin.page-header>

    <x-admin.table :empty="$roles->isEmpty()">
        <x-slot:head>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">Rol</th>
            <th class="px-4 py-3">Slug</th>
            <th class="px-4 py-3">İcazə sayı</th>
            <th class="px-4 py-3">İstifadəçilər</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Fəaliyyətlər</th>
        </x-slot:head>

        @foreach ($roles as $role)
            <tr wire:key="role-{{ $role->id }}" class="hover:bg-surface/50">
                <td class="px-4 py-3 text-muted">{{ $loop->iteration }}</td>
                <td class="px-4 py-3 font-semibold">{{ $role->name }}</td>
                <td class="px-4 py-3 font-mono text-xs">{{ $role->slug }}</td>
                <td class="px-4 py-3">{{ $role->isSuperAdmin() ? 'hamısı' : count($role->permissions) }}</td>
                <td class="px-4 py-3">{{ $role->users_count }}</td>
                <td class="px-4 py-3"><x-admin.status-badge :active="$role->status" /></td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-1">
                        @if ($this->can('update'))<x-admin.button variant="ghost" size="sm" :href="route('admin.roles.edit', $role)" wire:navigate aria-label="Redaktə et"><x-admin.icon name="pencil" class="size-4" /></x-admin.button>@endif
                        @if ($this->can('delete') && ! $role->isSuperAdmin())<x-admin.button variant="ghost" size="sm" wire:click="confirmDelete({{ $role->id }})" aria-label="Sil"><x-admin.icon name="trash-2" class="size-4" /></x-admin.button>@endif
                    </div>
                </td>
            </tr>
        @endforeach
    </x-admin.table>

    <x-admin.confirm-delete />
</div>
