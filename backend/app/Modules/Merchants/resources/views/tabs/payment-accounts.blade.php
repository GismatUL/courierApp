<div class="flex flex-col gap-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-muted">Partnyora ödənişin edildiyi hesablar.</p>
        @if ($this->can('create'))<x-admin.button wire:click="create"><x-admin.icon name="plus" class="size-4" /> Əlavə et</x-admin.button>@endif
    </div>

    <x-admin.table :empty="$accounts->isEmpty()">
        <x-slot:head>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">Ödəniş metodu</th>
            <th class="px-4 py-3">Hesab məlumatı</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Fəaliyyətlər</th>
        </x-slot:head>
        @foreach ($accounts as $account)
            <tr wire:key="account-{{ $account->id }}" class="hover:bg-surface/50">
                <td class="px-4 py-3 text-muted">{{ $loop->iteration }}</td>
                <td class="px-4 py-3 font-semibold">{{ $account->method->label() }}</td>
                <td class="px-4 py-3 font-mono text-xs">{{ $account->summary() }}</td>
                <td class="px-4 py-3"><x-admin.status-badge :active="$account->status" /></td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-1">
                        @if ($this->can('update'))<x-admin.button variant="ghost" size="sm" wire:click="edit({{ $account->id }})" aria-label="Redaktə et"><x-admin.icon name="pencil" class="size-4" /></x-admin.button>@endif
                        @if ($this->can('delete'))<x-admin.button variant="ghost" size="sm" wire:click="confirmDelete({{ $account->id }})" aria-label="Sil"><x-admin.icon name="trash-2" class="size-4" /></x-admin.button>@endif
                    </div>
                </td>
            </tr>
        @endforeach
    </x-admin.table>

    <x-admin.modal wire:model="showForm" :title="$form->id ? 'Ödəniş hesabını redaktə et' : 'Yeni ödəniş hesabı'">
        <form wire:submit="save" class="grid gap-4 sm:grid-cols-2">
            <x-admin.field label="Ödəniş metodu" for="p-method" error="form.method" required class="sm:col-span-2">
                <x-admin.select id="p-method" wire:model.live="form.method">
                    @foreach ($methods as $method)<option value="{{ $method->value }}">{{ $method->label() }}</option>@endforeach
                </x-admin.select>
            </x-admin.field>
            @foreach ($fields as $key => $field)
                <x-admin.field :label="$field['label']" for="p-{{ $key }}" error="form.details.{{ $key }}" :required="in_array('required', $field['rules'], true)" :class="in_array($key, ['iban', 'note'], true) ? 'sm:col-span-2' : ''">
                    <x-admin.input id="p-{{ $key }}" wire:model="form.details.{{ $key }}" :class="$key === 'iban' ? 'font-mono uppercase' : ''" :invalid="$errors->has('form.details.'.$key)" />
                </x-admin.field>
            @endforeach
            <label class="flex items-center gap-2 text-sm sm:col-span-2">
                <input type="checkbox" wire:model="form.status" class="size-4 accent-brand"> Aktiv
            </label>
            <div class="flex justify-end gap-2 sm:col-span-2">
                <x-admin.button variant="secondary" @click="show = false">İmtina</x-admin.button>
                <x-admin.button type="submit" wire:loading.attr="disabled" wire:target="save">Yadda saxla</x-admin.button>
            </div>
        </form>
    </x-admin.modal>

    <x-admin.confirm-delete />
</div>
