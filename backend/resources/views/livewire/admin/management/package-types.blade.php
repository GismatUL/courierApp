<div class="flex flex-col gap-5">
    <x-admin.page-header description="Paketin ölçü sinfi çəkiyə görə müəyyən edilir; tariflər paket növü üzrə qurulur.">
        <x-admin.button wire:click="create"><x-admin.icon name="plus" class="size-4" /> Əlavə et</x-admin.button>
    </x-admin.page-header>

    <x-admin.table :empty="$types->isEmpty()">
        <x-slot:head>
            <th class="px-4 py-3">ID</th>
            <th class="px-4 py-3">Ad</th>
            <th class="px-4 py-3">Minimal çəki (kq)</th>
            <th class="px-4 py-3">Maksimal çəki (kq)</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Fəaliyyətlər</th>
        </x-slot:head>

        @foreach ($types as $type)
            <tr wire:key="type-{{ $type->id }}" class="hover:bg-surface/50">
                <td class="px-4 py-3 text-muted">{{ $type->id }}</td>
                <td class="px-4 py-3">
                    <span class="font-semibold">{{ $type->name }}</span>
                    @if ($type->description)
                        <p class="text-xs text-muted">{{ $type->description }}</p>
                    @endif
                </td>
                <td class="px-4 py-3">{{ (float) $type->min_weight }}</td>
                <td class="px-4 py-3">{{ $type->max_weight === null ? 'limitsiz' : (float) $type->max_weight }}</td>
                <td class="px-4 py-3"><x-admin.status-badge :active="$type->status" /></td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-1">
                        <x-admin.button variant="ghost" size="sm" wire:click="edit({{ $type->id }})" aria-label="Redaktə et"><x-admin.icon name="pencil" class="size-4" /></x-admin.button>
                        <x-admin.button variant="ghost" size="sm" wire:click="confirmDelete({{ $type->id }})" aria-label="Sil"><x-admin.icon name="trash-2" class="size-4" /></x-admin.button>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-admin.table>

    <x-admin.modal wire:model="showForm" :title="$form->id ? 'Paket növünü redaktə et' : 'Yeni paket növü'">
        <form wire:submit="save" class="grid gap-4 sm:grid-cols-2">
            <x-admin.field label="Ad" for="pt-name" error="form.name" required class="sm:col-span-2">
                <x-admin.input id="pt-name" wire:model="form.name" placeholder="Bağlama10" :invalid="$errors->has('form.name')" />
            </x-admin.field>
            <x-admin.field label="Minimal çəki (kq)" for="pt-min" error="form.min_weight" required>
                <x-admin.input id="pt-min" wire:model="form.min_weight" inputmode="decimal" :invalid="$errors->has('form.min_weight')" />
            </x-admin.field>
            <x-admin.field label="Maksimal çəki (kq)" for="pt-max" error="form.max_weight" hint="Boş qalsa, yuxarı limit yoxdur (məs. Bağlama50+).">
                <x-admin.input id="pt-max" wire:model="form.max_weight" inputmode="decimal" :invalid="$errors->has('form.max_weight')" />
            </x-admin.field>
            <x-admin.field label="Təsvir" for="pt-desc" error="form.description" class="sm:col-span-2">
                <x-admin.textarea id="pt-desc" wire:model="form.description" rows="2" />
            </x-admin.field>
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
