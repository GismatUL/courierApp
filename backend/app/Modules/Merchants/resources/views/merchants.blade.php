<div class="flex flex-col gap-5">
    <x-admin.page-header description="Paket göndərən korporativ müştərilər: hər birinin öz tarifləri, yığma nöqtələri, ödəniş hesabları və balansı var.">
        @can('merchant_read')<x-admin.button variant="secondary" :href="route('admin.merchant-types.index')" wire:navigate>Partnyor növləri</x-admin.button>@endcan
        @if ($this->can('create'))<x-admin.button :href="route('admin.merchants.create')" wire:navigate><x-admin.icon name="plus" class="size-4" /> Əlavə et</x-admin.button>@endif
    </x-admin.page-header>

    <x-admin.filters>
        <x-admin.field label="Axtarış" for="f-q" class="w-full sm:w-64">
            <x-admin.input id="f-q" wire:model.live.debounce.300ms="search" placeholder="Ad, unikal ID, email, telefon…" />
        </x-admin.field>
        <x-admin.field label="Filial" for="f-hub" class="w-full sm:w-48">
            <x-admin.select id="f-hub" wire:model.live="hubId" placeholder="Hamısı">
                @foreach ($hubs as $hub)
                    <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                @endforeach
            </x-admin.select>
        </x-admin.field>
        <x-admin.field label="Status" for="f-status" class="w-full sm:w-36">
            <x-admin.select id="f-status" wire:model.live="status" placeholder="Hamısı">
                <option value="1">Aktiv</option>
                <option value="0">Passiv</option>
            </x-admin.select>
        </x-admin.field>
    </x-admin.filters>

    <x-admin.table :empty="$merchants->isEmpty()">
        <x-slot:head>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">Biznes adı</th>
            <th class="px-4 py-3">Unikal ID</th>
            <th class="px-4 py-3">Təfərrüatlar</th>
            <th class="px-4 py-3">Mərkəz</th>
            <th class="px-4 py-3 text-right">Cari balans</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Fəaliyyətlər</th>
        </x-slot:head>

        @foreach ($merchants as $merchant)
            <tr wire:key="merchant-{{ $merchant->id }}" class="hover:bg-surface/50">
                <td class="px-4 py-3 text-muted">{{ $merchants->firstItem() + $loop->index }}</td>
                <td class="px-4 py-3 font-semibold">
                    @if ($this->can('view'))
                        <a href="{{ route('admin.merchants.show', $merchant) }}" wire:navigate class="hover:text-brand">{{ $merchant->business_name }}</a>
                    @else
                        {{ $merchant->business_name }}
                    @endif
                </td>
                <td class="px-4 py-3 font-mono">{{ $merchant->unique_id }}</td>
                <td class="px-4 py-3">
                    <p>{{ $merchant->contact_name ?? '—' }}</p>
                    <p class="text-xs text-muted">{{ collect([$merchant->email, $merchant->mobile])->filter()->implode(' · ') ?: '—' }}</p>
                </td>
                <td class="px-4 py-3">{{ $merchant->hubs->pluck('name')->implode(', ') ?: '—' }}</td>
                <td class="px-4 py-3 text-right whitespace-nowrap {{ (float) $merchant->current_balance < 0 ? 'text-brand' : '' }}">{{ number_format($merchant->current_balance, 2, '.', ' ') }} ₼</td>
                <td class="px-4 py-3"><x-admin.status-badge :active="$merchant->status" /></td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-1">
                        @if ($this->can('view'))<x-admin.button variant="ghost" size="sm" :href="route('admin.merchants.show', $merchant)" wire:navigate aria-label="Bax"><x-admin.icon name="briefcase" class="size-4" /></x-admin.button>@endif
                        @if ($this->can('update'))<x-admin.button variant="ghost" size="sm" :href="route('admin.merchants.edit', $merchant)" wire:navigate aria-label="Redaktə et"><x-admin.icon name="pencil" class="size-4" /></x-admin.button>@endif
                        @if ($this->can('delete'))<x-admin.button variant="ghost" size="sm" wire:click="confirmDelete({{ $merchant->id }})" aria-label="Sil"><x-admin.icon name="trash-2" class="size-4" /></x-admin.button>@endif
                    </div>
                </td>
            </tr>
        @endforeach

        @if ($merchants->hasPages())
            <x-slot:footer>{{ $merchants->links() }}</x-slot:footer>
        @endif
    </x-admin.table>

    <x-admin.confirm-delete />
</div>
