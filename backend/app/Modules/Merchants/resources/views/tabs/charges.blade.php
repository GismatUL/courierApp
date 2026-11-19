<div class="flex flex-col gap-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-muted">Partnyorun fərdi qiyməti. Burada olmayan kombinasiyalar üçün zonanın tarif cədvəli tətbiq olunur.</p>
        @if ($this->can('create'))<x-admin.button wire:click="create"><x-admin.icon name="plus" class="size-4" /> Əlavə et</x-admin.button>@endif
    </div>

    <x-admin.table :empty="$charges->isEmpty()">
        <x-slot:head>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">Zona</th>
            <th class="px-4 py-3">Paket növü</th>
            <th class="px-4 py-3">Xidmət növü</th>
            <th class="px-4 py-3">Daşınma müddəti</th>
            <th class="px-4 py-3">Götürülmə müddəti</th>
            <th class="px-4 py-3 text-right">Qiymət</th>
            <th class="px-4 py-3 text-right">Fəaliyyətlər</th>
        </x-slot:head>
        @foreach ($charges as $charge)
            <tr wire:key="charge-{{ $charge->id }}" class="hover:bg-surface/50">
                <td class="px-4 py-3 text-muted">{{ $loop->iteration }}</td>
                <td class="px-4 py-3">{{ $charge->zone->name }}</td>
                <td class="px-4 py-3">{{ $charge->packageType->name }}</td>
                <td class="px-4 py-3">{{ $charge->tariff_type->label() }}</td>
                <td class="px-4 py-3">{{ $charge->tracking_time ?? '—' }}</td>
                <td class="px-4 py-3">{{ $charge->delivery_time ?? '—' }}</td>
                <td class="px-4 py-3 text-right font-semibold whitespace-nowrap">{{ number_format($charge->price, 2, '.', ' ') }} ₼</td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-1">
                        @if ($this->can('update'))<x-admin.button variant="ghost" size="sm" wire:click="edit({{ $charge->id }})" aria-label="Redaktə et"><x-admin.icon name="pencil" class="size-4" /></x-admin.button>@endif
                        @if ($this->can('delete'))<x-admin.button variant="ghost" size="sm" wire:click="confirmDelete({{ $charge->id }})" aria-label="Sil"><x-admin.icon name="trash-2" class="size-4" /></x-admin.button>@endif
                    </div>
                </td>
            </tr>
        @endforeach
    </x-admin.table>

    <x-admin.modal wire:model="showForm" :title="$form->id ? 'Tarifi redaktə et' : 'Yeni tarif'">
        <form wire:submit="save" class="grid gap-4 sm:grid-cols-2">
            <x-admin.field label="Zona" for="c-zone" error="form.zone_id" required>
                <x-admin.select id="c-zone" wire:model="form.zone_id" placeholder="Seçin" :invalid="$errors->has('form.zone_id')">
                    @foreach ($zones as $zone)<option value="{{ $zone->id }}">{{ $zone->name }}</option>@endforeach
                </x-admin.select>
            </x-admin.field>
            <x-admin.field label="Paket növü" for="c-type" error="form.package_type_id" required>
                <x-admin.select id="c-type" wire:model="form.package_type_id" placeholder="Seçin" :invalid="$errors->has('form.package_type_id')">
                    @foreach ($packageTypes as $type)<option value="{{ $type->id }}">{{ $type->name }}</option>@endforeach
                </x-admin.select>
            </x-admin.field>
            <x-admin.field label="Xidmət növü" for="c-tariff" error="form.tariff_type" required>
                <x-admin.select id="c-tariff" wire:model="form.tariff_type" placeholder="Seçin" :invalid="$errors->has('form.tariff_type')">
                    @foreach ($tariffs as $tariff)<option value="{{ $tariff->value }}">{{ $tariff->label() }}</option>@endforeach
                </x-admin.select>
            </x-admin.field>
            <x-admin.field label="Qiymət (AZN)" for="c-price" error="form.price" required>
                <x-admin.input id="c-price" wire:model="form.price" inputmode="decimal" :invalid="$errors->has('form.price')" />
            </x-admin.field>
            <x-admin.field label="Daşınma müddəti" for="c-track" error="form.tracking_time">
                <x-admin.input id="c-track" wire:model="form.tracking_time" placeholder="1 gün" />
            </x-admin.field>
            <x-admin.field label="Götürülmə müddəti" for="c-deliv" error="form.delivery_time">
                <x-admin.input id="c-deliv" wire:model="form.delivery_time" placeholder="1 gün" />
            </x-admin.field>
            <div class="flex justify-end gap-2 sm:col-span-2">
                <x-admin.button variant="secondary" @click="show = false">İmtina</x-admin.button>
                <x-admin.button type="submit" wire:loading.attr="disabled" wire:target="save">Yadda saxla</x-admin.button>
            </div>
        </form>
    </x-admin.modal>

    <x-admin.confirm-delete />
</div>
