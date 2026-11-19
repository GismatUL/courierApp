<div class="flex flex-col gap-5">
    <x-admin.page-header description="Bütün paketlər: filtr, toplu əməliyyat, etiket çapı və Excel.">
        @can('parcel_read')<x-admin.button variant="secondary" :href="route('admin.parcels.export', array_filter(['dateFrom' => $dateFrom, 'dateTo' => $dateTo, 'status' => $status, 'merchant' => $merchantId, 'shops' => $shopIds, 'courier' => $courierId, 'invoice' => $invoice, 'address' => $address, 'q' => $search, 'by' => $createdBy]))">Excel</x-admin.button>@endcan
        @if ($this->can('create'))
            <x-admin.button variant="secondary" :href="route('admin.parcels.import')" wire:navigate>Import Excel</x-admin.button>
            <x-admin.button :href="route('admin.parcels.create')" wire:navigate><x-admin.icon name="plus" class="size-4" /> Əlavə et</x-admin.button>
        @endif
    </x-admin.page-header>

    <x-admin.filters>
        <x-admin.field label="Axtarış" for="f-q" class="w-full sm:w-52"><x-admin.input id="f-q" wire:model.live.debounce.300ms="search" placeholder="İzləmə kodu, ad, telefon" /></x-admin.field>
        <x-admin.field label="Tarixdən" for="f-from" class="w-full sm:w-40"><x-admin.input id="f-from" type="date" wire:model.live="dateFrom" /></x-admin.field>
        <x-admin.field label="Tarixədək" for="f-to" class="w-full sm:w-40"><x-admin.input id="f-to" type="date" wire:model.live="dateTo" /></x-admin.field>
        <x-admin.field label="Status" for="f-status" class="w-full sm:w-52">
            <x-admin.select id="f-status" wire:model.live="status" placeholder="Hamısı">
                @foreach ($statuses as $s)<option value="{{ $s->value }}">{{ $s->label() }}</option>@endforeach
            </x-admin.select>
        </x-admin.field>
        <x-admin.field label="Partnyor" for="f-merchant" class="w-full sm:w-48">
            <x-admin.select id="f-merchant" wire:model.live="merchantId" placeholder="Hamısı">
                @foreach ($merchants as $m)<option value="{{ $m->id }}">{{ $m->business_name }}</option>@endforeach
            </x-admin.select>
        </x-admin.field>
        @if ($shops->isNotEmpty())
            <x-admin.field label="Filial" for="f-shops" class="w-full sm:w-48">
                <x-admin.select id="f-shops" wire:model.live="shopIds" multiple size="3">
                    @foreach ($shops as $shop)<option value="{{ $shop->id }}">{{ $shop->name }}</option>@endforeach
                </x-admin.select>
            </x-admin.field>
        @endif
        <x-admin.field label="Kuryer" for="f-courier" class="w-full sm:w-44">
            <x-admin.select id="f-courier" wire:model.live="courierId" placeholder="Hamısı">
                @foreach ($couriers as $c)<option value="{{ $c->id }}">{{ $c->user->name }}</option>@endforeach
            </x-admin.select>
        </x-admin.field>
        <x-admin.field label="Qaimə №" for="f-invoice" class="w-full sm:w-36"><x-admin.input id="f-invoice" wire:model.live.debounce.300ms="invoice" /></x-admin.field>
        <x-admin.field label="Ünvan" for="f-address" class="w-full sm:w-44"><x-admin.input id="f-address" wire:model.live.debounce.300ms="address" /></x-admin.field>
        <x-admin.field label="Operator" for="f-by" class="w-full sm:w-44">
            <x-admin.select id="f-by" wire:model.live="createdBy" placeholder="Hamısı">
                @foreach ($creators as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach
            </x-admin.select>
        </x-admin.field>
    </x-admin.filters>

    <div class="flex flex-wrap items-center gap-2">
        <span class="text-sm text-muted">{{ count($selected) }} seçilib · cəmi {{ $parcels->total() }}</span>
        @if ($this->can('status'))<x-admin.button variant="secondary" size="sm" wire:click="bulk">Toplu əməliyyat / skan</x-admin.button>@endif
        <x-admin.button variant="secondary" size="sm" wire:click="printSelected" :disabled="! $selected"><x-admin.icon name="printer" class="size-4" /> Etiketləri çap et</x-admin.button>
    </div>

    <x-admin.table :empty="$parcels->isEmpty()">
        <x-slot:head>
            <th class="px-3 py-3"><span class="sr-only">Seç</span></th>
            <th class="px-3 py-3">Qaimə №</th>
            <th class="px-3 py-3">Partnyor</th>
            <th class="px-3 py-3">Qəbul edən</th>
            <th class="px-3 py-3 text-right">Məbləğ</th>
            <th class="px-3 py-3">Zona</th>
            <th class="px-3 py-3">Status</th>
            <th class="px-3 py-3">Kuryer</th>
            <th class="px-3 py-3 text-right">Fəaliyyətlər</th>
        </x-slot:head>

        @foreach ($parcels as $parcel)
            <tr wire:key="parcel-{{ $parcel->id }}" class="align-top hover:bg-surface/50">
                <td class="px-3 py-3"><input type="checkbox" value="{{ $parcel->id }}" wire:model.live="selected" class="size-4 accent-brand" aria-label="{{ $parcel->tracking_id }} seç"></td>
                <td class="px-3 py-3">
                    <a href="{{ route('admin.parcels.show', $parcel) }}" wire:navigate class="font-mono text-xs font-semibold hover:text-brand">{{ $parcel->tracking_id }}</a>
                    <p class="text-xs text-muted">№ {{ $parcel->invoice_no }}{{ $parcel->merchant_invoice_no ? ' · '.$parcel->merchant_invoice_no : '' }}</p>
                </td>
                <td class="px-3 py-3"><p class="font-medium">{{ $parcel->merchant->business_name }}</p></td>
                <td class="px-3 py-3">
                    <p class="font-medium">{{ $parcel->customer_name }}</p>
                    <p class="text-xs text-muted">{{ $parcel->customer_phone }}</p>
                    <p class="max-w-56 truncate text-xs text-muted" title="{{ $parcel->customer_address }}">{{ $parcel->customer_address }}</p>
                </td>
                <td class="px-3 py-3 text-right whitespace-nowrap">
                    <p class="font-semibold">{{ $parcel->total }} ₼</p>
                    <p class="text-xs text-muted">{{ $parcel->delivery_charge }} × {{ $parcel->parcel_count }} · COD {{ $parcel->cash_collection }}</p>
                </td>
                <td class="px-3 py-3">{{ $parcel->zone->name }}</td>
                <td class="px-3 py-3">
                    <x-parcels::status-badge :status="$parcel->status" />
                    <p class="mt-1 text-xs text-muted">{{ $parcel->status_updated_at?->timezone('Asia/Baku')->format('d.m.Y H:i') }}</p>
                    @if ($parcel->rejectReason)<p class="text-xs text-brand">{{ $parcel->rejectReason->name }}</p>@endif
                </td>
                <td class="px-3 py-3">{{ $parcel->deliveryMan?->user->name ?? '—' }}</td>
                <td class="px-3 py-3">
                    <div class="flex justify-end gap-1">
                        @if ($this->can('status') && $actionsByStatus[$parcel->status->value])
                            <x-admin.button variant="secondary" size="sm" wire:click="$dispatch('parcel-action', { parcelId: {{ $parcel->id }} })">Status</x-admin.button>
                        @endif
                        <x-admin.button variant="ghost" size="sm" :href="route('admin.parcels.labels', ['ids' => $parcel->id])" target="_blank" aria-label="Etiket çap et"><x-admin.icon name="printer" class="size-4" /></x-admin.button>
                        @if ($this->can('update') && $parcel->status->value === 1)<x-admin.button variant="ghost" size="sm" :href="route('admin.parcels.edit', $parcel)" wire:navigate aria-label="Redaktə et"><x-admin.icon name="pencil" class="size-4" /></x-admin.button>@endif
                        @if ($this->can('delete') && $parcel->status->value === 1)<x-admin.button variant="ghost" size="sm" wire:click="confirmDelete({{ $parcel->id }})" aria-label="Sil"><x-admin.icon name="trash-2" class="size-4" /></x-admin.button>@endif
                    </div>
                </td>
            </tr>
        @endforeach

        @if ($parcels->hasPages())
            <x-slot:footer>{{ $parcels->links() }}</x-slot:footer>
        @endif
    </x-admin.table>

    <x-admin.confirm-delete />
    <livewire:parcels-action-panel />
</div>
