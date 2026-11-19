<div class="flex flex-col gap-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-muted">Kuryerin bağlamanı götürdüyü nöqtələr. Əsas nöqtə yeni paketlərdə avtomatik seçilir.</p>
        @if ($this->can('create'))<x-admin.button wire:click="create"><x-admin.icon name="plus" class="size-4" /> Əlavə et</x-admin.button>@endif
    </div>

    <x-admin.table :empty="$shops->isEmpty()">
        <x-slot:head>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">Ad</th>
            <th class="px-4 py-3">Əlaqə</th>
            <th class="px-4 py-3">Ünvan</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Fəaliyyətlər</th>
        </x-slot:head>
        @foreach ($shops as $shop)
            <tr wire:key="shop-{{ $shop->id }}" class="hover:bg-surface/50">
                <td class="px-4 py-3 text-muted">{{ $loop->iteration }}</td>
                <td class="px-4 py-3 font-semibold">{{ $shop->name }}</td>
                <td class="px-4 py-3 whitespace-nowrap">{{ $shop->contact_no ?? '—' }}</td>
                <td class="px-4 py-3">
                    {{ $shop->address }}
                    <a href="https://www.google.com/maps?q={{ $shop->latitude }},{{ $shop->longitude }}" target="_blank" rel="noopener noreferrer" class="ml-1 inline-flex items-center gap-0.5 text-xs font-medium text-brand hover:underline"><x-admin.icon name="map-pin" class="size-3.5" />xəritə</a>
                </td>
                <td class="px-4 py-3">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <x-admin.status-badge :active="$shop->status" />
                        @if ($shop->is_default)<span class="rounded-full bg-brand-surface px-2 py-1 text-xs font-semibold text-brand">Təyin edilmiş</span>@endif
                    </div>
                </td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-1">
                        @if ($this->can('update') && ! $shop->is_default)<x-admin.button variant="ghost" size="sm" wire:click="makeDefault({{ $shop->id }})">Əsas et</x-admin.button>@endif
                        @if ($this->can('update'))<x-admin.button variant="ghost" size="sm" wire:click="edit({{ $shop->id }})" aria-label="Redaktə et"><x-admin.icon name="pencil" class="size-4" /></x-admin.button>@endif
                        @if ($this->can('delete'))<x-admin.button variant="ghost" size="sm" wire:click="confirmDelete({{ $shop->id }})" aria-label="Sil"><x-admin.icon name="trash-2" class="size-4" /></x-admin.button>@endif
                    </div>
                </td>
            </tr>
        @endforeach
    </x-admin.table>

    <x-admin.modal wire:model="showForm" :title="$form->id ? 'Yığma nöqtəsini redaktə et' : 'Yeni yığma nöqtəsi'">
        <form wire:submit="save" class="grid gap-4 sm:grid-cols-2">
            <x-admin.field label="Ad" for="s-name" error="form.name" required>
                <x-admin.input id="s-name" wire:model="form.name" :invalid="$errors->has('form.name')" />
            </x-admin.field>
            <x-admin.field label="Əlaqə nömrəsi" for="s-contact" error="form.contact_no">
                <x-admin.input id="s-contact" wire:model="form.contact_no" :invalid="$errors->has('form.contact_no')" />
            </x-admin.field>
            <x-admin.field label="Ünvan" for="s-address" error="form.address" required class="sm:col-span-2">
                <x-admin.input id="s-address" wire:model="form.address" :invalid="$errors->has('form.address')" />
            </x-admin.field>
            <x-admin.field label="Enlik (lat)" for="s-lat" error="form.latitude" required>
                <x-admin.input id="s-lat" wire:model="form.latitude" inputmode="decimal" placeholder="40.4093" :invalid="$errors->has('form.latitude')" />
            </x-admin.field>
            <x-admin.field label="Uzunluq (long)" for="s-lng" error="form.longitude" required>
                <x-admin.input id="s-lng" wire:model="form.longitude" inputmode="decimal" placeholder="49.8671" :invalid="$errors->has('form.longitude')" />
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
