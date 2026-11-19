<div class="flex flex-col gap-5">
    <x-admin.page-header description="Partnyorların qruplaşdırılması üçün növlər.">
        <x-admin.button variant="secondary" :href="route('admin.merchants.index')" wire:navigate>Partnyorlar</x-admin.button>
        @if ($this->can('create'))<x-admin.button wire:click="create"><x-admin.icon name="plus" class="size-4" /> Əlavə et</x-admin.button>@endif
    </x-admin.page-header>

    <x-admin.filters>
        <x-admin.field label="Axtarış" for="f-q" class="w-full sm:w-56">
            <x-admin.input id="f-q" wire:model.live.debounce.300ms="q" placeholder="Başlıq…" />
        </x-admin.field>
        <label class="flex items-center gap-2 pb-2.5 text-sm"><input type="checkbox" wire:model.live="hasMerchants" class="size-4 accent-brand"> Yalnız partnyoru olanlar</label>
    </x-admin.filters>

    <x-admin.table :empty="$types->isEmpty()">
        <x-slot:head>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">Başlıq</th>
            <th class="px-4 py-3">Partnyorlar</th>
            <th class="px-4 py-3 text-right">Əməliyyatlar</th>
        </x-slot:head>
        @foreach ($types as $type)
            <tr wire:key="type-{{ $type->id }}" class="hover:bg-surface/50">
                <td class="px-4 py-3 text-muted">{{ $loop->iteration }}</td>
                <td class="px-4 py-3 font-semibold">{{ $type->title }}</td>
                <td class="px-4 py-3">{{ $type->merchants_count }}</td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-1">
                        @if ($this->can('update'))<x-admin.button variant="ghost" size="sm" wire:click="edit({{ $type->id }})" aria-label="Redaktə et"><x-admin.icon name="pencil" class="size-4" /></x-admin.button>@endif
                        @if ($this->can('delete'))<x-admin.button variant="ghost" size="sm" wire:click="confirmDelete({{ $type->id }})" aria-label="Sil"><x-admin.icon name="trash-2" class="size-4" /></x-admin.button>@endif
                    </div>
                </td>
            </tr>
        @endforeach
    </x-admin.table>

    <x-admin.modal wire:model="showForm" :title="$form->id ? 'Növü redaktə et' : 'Yeni növ'">
        <form wire:submit="save" class="grid gap-4">
            <x-admin.field label="Başlıq" for="mt-title" error="form.title" required>
                <x-admin.input id="mt-title" wire:model="form.title" :invalid="$errors->has('form.title')" />
            </x-admin.field>
            <div class="flex justify-end gap-2">
                <x-admin.button variant="secondary" @click="show = false">İmtina</x-admin.button>
                <x-admin.button type="submit" wire:loading.attr="disabled" wire:target="save">Yadda saxla</x-admin.button>
            </div>
        </form>
    </x-admin.modal>

    <x-admin.confirm-delete />
</div>
