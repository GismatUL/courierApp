<div class="flex flex-col gap-5">
    <x-admin.page-header description="Zonanın tarif cədvəli partnyorun fərdi tarifi olmadıqda baza qiymətdir.">
        <x-admin.button variant="secondary" :href="route('admin.regions.index')" wire:navigate><x-admin.icon name="layers" class="size-4" /> Regionlar</x-admin.button>
        <x-admin.button wire:click="create"><x-admin.icon name="plus" class="size-4" /> Əlavə et</x-admin.button>
    </x-admin.page-header>

    <x-admin.filters>
        <x-admin.field label="Ad" for="f-q" class="w-full sm:w-56">
            <x-admin.input id="f-q" wire:model.live.debounce.300ms="search" placeholder="Axtar…" />
        </x-admin.field>
        <x-admin.field label="Filial" for="f-hub" class="w-full sm:w-56">
            <x-admin.select id="f-hub" wire:model.live="hubId" placeholder="Hamısı">
                @foreach ($hubs as $hub)
                    <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                @endforeach
            </x-admin.select>
        </x-admin.field>
    </x-admin.filters>

    <x-admin.table :empty="$zones->isEmpty()">
        <x-slot:head>
            <th class="px-4 py-3">ID</th>
            <th class="px-4 py-3">Ad</th>
            <th class="px-4 py-3">Filial</th>
            <th class="px-4 py-3">Tariflər</th>
            <th class="px-4 py-3">Regionlar</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Fəaliyyətlər</th>
        </x-slot:head>

        @foreach ($zones as $zone)
            <tr wire:key="zone-{{ $zone->id }}" class="hover:bg-surface/50">
                <td class="px-4 py-3 text-muted">{{ $zone->id }}</td>
                <td class="px-4 py-3">
                    <span class="font-semibold">{{ $zone->name }}</span>
                    @if ($zone->note)
                        <p class="text-xs text-muted">{{ $zone->note }}</p>
                    @endif
                </td>
                <td class="px-4 py-3">{{ $zone->hub?->name ?? '—' }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('admin.zones.tariffs', $zone) }}" wire:navigate class="font-medium text-brand hover:underline">{{ $zone->tariffs_count }} qiymət</a>
                </td>
                <td class="px-4 py-3">
                    <a href="{{ route('admin.regions.index', ['zone' => $zone->id]) }}" wire:navigate class="font-medium text-brand hover:underline">{{ $zone->regions_count }}</a>
                </td>
                <td class="px-4 py-3"><x-admin.status-badge :active="$zone->status" /></td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-1">
                        <x-admin.button variant="ghost" size="sm" :href="route('admin.zones.tariffs', $zone)" wire:navigate aria-label="Tarif cədvəli"><x-admin.icon name="table" class="size-4" /></x-admin.button>
                        <x-admin.button variant="ghost" size="sm" wire:click="edit({{ $zone->id }})" aria-label="Redaktə et"><x-admin.icon name="pencil" class="size-4" /></x-admin.button>
                        <x-admin.button variant="ghost" size="sm" wire:click="confirmDelete({{ $zone->id }})" aria-label="Sil"><x-admin.icon name="trash-2" class="size-4" /></x-admin.button>
                    </div>
                </td>
            </tr>
        @endforeach

        @if ($zones->hasPages())
            <x-slot:footer>{{ $zones->links() }}</x-slot:footer>
        @endif
    </x-admin.table>

    <x-admin.modal wire:model="showForm" :title="$form->id ? 'Zonanı redaktə et' : 'Yeni zona'">
        <form wire:submit="save" class="grid gap-4">
            <x-admin.field label="Ad" for="zone-name" error="form.name" required>
                <x-admin.input id="zone-name" wire:model="form.name" placeholder="1-ci zona" :invalid="$errors->has('form.name')" />
            </x-admin.field>
            <x-admin.field label="Filial" for="zone-hub" error="form.hub_id">
                <x-admin.select id="zone-hub" wire:model="form.hub_id" placeholder="Seçin">
                    @foreach ($hubs as $hub)
                        <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                    @endforeach
                </x-admin.select>
            </x-admin.field>
            <x-admin.field label="Qeyd" for="zone-note" error="form.note">
                <x-admin.textarea id="zone-note" wire:model="form.note" rows="2" />
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
