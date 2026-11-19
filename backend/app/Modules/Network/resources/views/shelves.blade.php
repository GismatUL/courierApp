<div class="flex flex-col gap-5">
    <x-admin.page-header description="Anbar çeşidləməsi üçün qutular: hər qutu bir zonaya / kuryer marşrutuna aiddir.">
        @if ($this->can('create'))<x-admin.button wire:click="create"><x-admin.icon name="plus" class="size-4" /> Əlavə et</x-admin.button>@endif
    </x-admin.page-header>

    <x-admin.filters>
        <x-admin.field label="Ad və ya kod" for="f-q" class="w-full sm:w-56">
            <x-admin.input id="f-q" wire:model.live.debounce.300ms="search" placeholder="Axtar…" />
        </x-admin.field>
        <x-admin.field label="Zona" for="f-zone" class="w-full sm:w-56">
            <x-admin.select id="f-zone" wire:model.live="zoneId" placeholder="Hamısı">
                @foreach ($zones as $zone)
                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                @endforeach
            </x-admin.select>
        </x-admin.field>
    </x-admin.filters>

    <x-admin.table :empty="$shelves->isEmpty()">
        <x-slot:head>
            <th class="px-4 py-3">Ad</th>
            <th class="px-4 py-3">Kod</th>
            <th class="px-4 py-3">Zona</th>
            <th class="px-4 py-3">Bağlama sayı</th>
            <th class="px-4 py-3">Limit</th>
            <th class="px-4 py-3">QR</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Əməliyyat</th>
        </x-slot:head>

        @foreach ($shelves as $shelf)
            <tr wire:key="shelf-{{ $shelf->id }}" class="hover:bg-surface/50">
                <td class="px-4 py-3 font-semibold">{{ $shelf->name }}</td>
                <td class="px-4 py-3 font-mono">{{ $shelf->code }}</td>
                <td class="px-4 py-3">{{ $shelf->zone->name }}</td>
                <td class="px-4 py-3 text-muted" title="Paket modulu əlavə edildikdən sonra hesablanacaq">—</td>
                <td class="px-4 py-3">{{ $shelf->limit !== null ? number_format($shelf->limit, 0, '.', ' ') : 'limitsiz' }}</td>
                <td class="px-4 py-3"><span class="block size-12 [&_svg]:size-full">{!! \App\Shared\Support\QrCode::svg($shelf->code, 48) !!}</span></td>
                <td class="px-4 py-3"><x-admin.status-badge :active="$shelf->status" /></td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-1">
                        <x-admin.button variant="ghost" size="sm" :href="route('admin.shelves.label', $shelf)" target="_blank" aria-label="Etiketi çap et"><x-admin.icon name="printer" class="size-4" /></x-admin.button>
                        @if ($this->can('update'))<x-admin.button variant="ghost" size="sm" wire:click="edit({{ $shelf->id }})" aria-label="Redaktə et"><x-admin.icon name="pencil" class="size-4" /></x-admin.button>@endif
                        @if ($this->can('delete'))<x-admin.button variant="ghost" size="sm" wire:click="confirmDelete({{ $shelf->id }})" aria-label="Sil"><x-admin.icon name="trash-2" class="size-4" /></x-admin.button>@endif
                    </div>
                </td>
            </tr>
        @endforeach

        @if ($shelves->hasPages())
            <x-slot:footer>{{ $shelves->links() }}</x-slot:footer>
        @endif
    </x-admin.table>

    <x-admin.modal wire:model="showForm" :title="$form->id ? 'Qutunu redaktə et' : 'Yeni qutu'">
        <form wire:submit="save" class="grid gap-4 sm:grid-cols-2">
            <x-admin.field label="Ad" for="shelf-name" error="form.name" required class="sm:col-span-2">
                <x-admin.input id="shelf-name" wire:model="form.name" placeholder="Sumqayıt / Qaradağ" :invalid="$errors->has('form.name')" />
            </x-admin.field>
            <x-admin.field label="Kod" for="shelf-code" error="form.code" required>
                <x-admin.input id="shelf-code" wire:model="form.code" placeholder="04A" class="font-mono uppercase" :invalid="$errors->has('form.code')" />
            </x-admin.field>
            <x-admin.field label="Limit" for="shelf-limit" error="form.limit" hint="Boş qalsa, limitsiz.">
                <x-admin.input id="shelf-limit" wire:model="form.limit" inputmode="numeric" :invalid="$errors->has('form.limit')" />
            </x-admin.field>
            <x-admin.field label="Zona" for="shelf-zone" error="form.zone_id" required class="sm:col-span-2">
                <x-admin.select id="shelf-zone" wire:model="form.zone_id" placeholder="Seçin" :invalid="$errors->has('form.zone_id')">
                    @foreach ($zones as $zone)
                        <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                    @endforeach
                </x-admin.select>
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
