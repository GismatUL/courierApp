<div class="flex flex-col gap-5">
    <x-admin.page-header description="Çatdırılmanın uğursuz olma səbəbi; paket statusunun altında göstərilir.">
        @if ($this->can('create'))<x-admin.button wire:click="create"><x-admin.icon name="plus" class="size-4" /> Əlavə et</x-admin.button>@endif
    </x-admin.page-header>

    <x-admin.table :empty="$reasons->isEmpty()">
        <x-slot:head>
            <th class="px-4 py-3">ID</th>
            <th class="px-4 py-3">Ad</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Fəaliyyətlər</th>
        </x-slot:head>

        @foreach ($reasons as $reason)
            <tr wire:key="reason-{{ $reason->id }}" class="hover:bg-surface/50">
                <td class="px-4 py-3 text-muted">{{ $reason->id }}</td>
                <td class="px-4 py-3 font-semibold">{{ $reason->name }}</td>
                <td class="px-4 py-3"><x-admin.status-badge :active="$reason->status" /></td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-1">
                        @if ($this->can('update'))<x-admin.button variant="ghost" size="sm" wire:click="edit({{ $reason->id }})" aria-label="Redaktə et"><x-admin.icon name="pencil" class="size-4" /></x-admin.button>@endif
                        @if ($this->can('delete'))<x-admin.button variant="ghost" size="sm" wire:click="confirmDelete({{ $reason->id }})" aria-label="Sil"><x-admin.icon name="trash-2" class="size-4" /></x-admin.button>@endif
                    </div>
                </td>
            </tr>
        @endforeach
    </x-admin.table>

    <x-admin.modal wire:model="showForm" :title="$form->id ? 'Səbəbi redaktə et' : 'Yeni səbəb'">
        <form wire:submit="save" class="grid gap-4">
            <x-admin.field label="Ad" for="reason-name" error="form.name" required>
                <x-admin.input id="reason-name" wire:model="form.name" :invalid="$errors->has('form.name')" />
            </x-admin.field>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" wire:model="form.status" class="size-4 accent-brand"> Aktiv
            </label>
            <div class="flex justify-end gap-2">
                <x-admin.button variant="secondary" @click="show = false">İmtina</x-admin.button>
                <x-admin.button type="submit" wire:loading.attr="disabled" wire:target="save">Yadda saxla</x-admin.button>
            </div>
        </form>
    </x-admin.modal>

    <x-admin.confirm-delete />
</div>
