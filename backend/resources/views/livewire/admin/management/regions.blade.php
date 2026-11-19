<div class="flex flex-col gap-5">
    <x-admin.page-header description="Zonaya aid alt ərazilər.">
        <x-admin.button variant="secondary" :href="route('admin.zones.index')" wire:navigate>Zonalar</x-admin.button>
        <x-admin.button wire:click="create"><x-admin.icon name="plus" class="size-4" /> Əlavə et</x-admin.button>
    </x-admin.page-header>

    <x-admin.filters>
        <x-admin.field label="Zona" for="f-zone" class="w-full sm:w-56">
            <x-admin.select id="f-zone" wire:model.live="zoneId" placeholder="Hamısı">
                @foreach ($zones as $zone)
                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                @endforeach
            </x-admin.select>
        </x-admin.field>
    </x-admin.filters>

    <x-admin.table :empty="$regions->isEmpty()">
        <x-slot:head>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">Ad</th>
            <th class="px-4 py-3">Zona</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Fəaliyyətlər</th>
        </x-slot:head>

        @foreach ($regions as $region)
            <tr wire:key="region-{{ $region->id }}" class="hover:bg-surface/50">
                <td class="px-4 py-3 text-muted">{{ $regions->firstItem() + $loop->index }}</td>
                <td class="px-4 py-3 font-semibold">{{ $region->name }}</td>
                <td class="px-4 py-3">{{ $region->zone->name }}</td>
                <td class="px-4 py-3"><x-admin.status-badge :active="$region->status" /></td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-1">
                        <x-admin.button variant="ghost" size="sm" wire:click="edit({{ $region->id }})" aria-label="Redaktə et"><x-admin.icon name="pencil" class="size-4" /></x-admin.button>
                        <x-admin.button variant="ghost" size="sm" wire:click="confirmDelete({{ $region->id }})" aria-label="Sil"><x-admin.icon name="trash-2" class="size-4" /></x-admin.button>
                    </div>
                </td>
            </tr>
        @endforeach

        @if ($regions->hasPages())
            <x-slot:footer>{{ $regions->links() }}</x-slot:footer>
        @endif
    </x-admin.table>

    <x-admin.modal wire:model="showForm" :title="$form->id ? 'Regionu redaktə et' : 'Yeni region'">
        <form wire:submit="save" class="grid gap-4">
            <x-admin.field label="Ad" for="region-name" error="form.name" required>
                <x-admin.input id="region-name" wire:model="form.name" :invalid="$errors->has('form.name')" />
            </x-admin.field>
            <x-admin.field label="Zona" for="region-zone" error="form.zone_id" required>
                <x-admin.select id="region-zone" wire:model="form.zone_id" placeholder="Seçin" :invalid="$errors->has('form.zone_id')">
                    @foreach ($zones as $zone)
                        <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                    @endforeach
                </x-admin.select>
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
