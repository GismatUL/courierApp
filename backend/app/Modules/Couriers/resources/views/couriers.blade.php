<div class="flex flex-col gap-5">
    <x-admin.page-header description="Kuryerlər mobil tətbiqə daxil olur (admin panelə yox). Balans 3 kanal üzrə ayrıca saxlanılır.">
        @if ($this->can('create'))<x-admin.button :href="route('admin.couriers.create')" wire:navigate><x-admin.icon name="plus" class="size-4" /> Əlavə et</x-admin.button>@endif
    </x-admin.page-header>

    <x-admin.filters>
        <x-admin.field label="Filial" for="f-hub" class="w-full sm:w-44">
            <x-admin.select id="f-hub" wire:model.live="hubId" placeholder="Hamısı">
                @foreach ($hubs as $hub)<option value="{{ $hub->id }}">{{ $hub->name }}</option>@endforeach
            </x-admin.select>
        </x-admin.field>
        <x-admin.field label="Ad" for="f-name" class="w-full sm:w-44"><x-admin.input id="f-name" wire:model.live.debounce.300ms="name" placeholder="Axtar…" /></x-admin.field>
        <x-admin.field label="Email" for="f-email" class="w-full sm:w-44"><x-admin.input id="f-email" wire:model.live.debounce.300ms="email" placeholder="Axtar…" /></x-admin.field>
        <x-admin.field label="Telefon" for="f-phone" class="w-full sm:w-44"><x-admin.input id="f-phone" wire:model.live.debounce.300ms="phone" placeholder="Axtar…" /></x-admin.field>
    </x-admin.filters>

    <x-admin.table :empty="$couriers->isEmpty()">
        <x-slot:head>
            <th class="px-4 py-3">ID</th>
            <th class="px-4 py-3">İstifadəçi</th>
            <th class="px-4 py-3">Mərkəz</th>
            <th class="px-4 py-3">Kuryer növü</th>
            <th class="px-4 py-3 text-right">Min. paket</th>
            <th class="px-4 py-3 text-right">Çatdırılma haqqı</th>
            <th class="px-4 py-3 text-right">Qaytarma haqqı</th>
            <th class="px-4 py-3 text-right">Cari balans</th>
            <th class="px-4 py-3 text-right">Nağd</th>
            <th class="px-4 py-3 text-right">P/Terminal</th>
            <th class="px-4 py-3 text-right">CardToCard</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Fəaliyyətlər</th>
        </x-slot:head>

        @foreach ($couriers as $courier)
            <tr wire:key="courier-{{ $courier->id }}" class="hover:bg-surface/50">
                <td class="px-4 py-3 text-muted">{{ $courier->id }}</td>
                <td class="px-4 py-3">
                    <p class="font-semibold">{{ $courier->user->name }}</p>
                    <p class="text-xs text-muted">{{ collect([$courier->user->email, $courier->user->mobile])->filter()->implode(' · ') }}</p>
                </td>
                <td class="px-4 py-3">{{ $courier->hub->name }}</td>
                <td class="px-4 py-3 whitespace-nowrap">
                    {{ $courier->type->label() }}
                    <p class="text-xs text-muted">{{ $courier->car_type->label() }}{{ $courier->car_number ? ' · '.$courier->car_number : '' }}</p>
                </td>
                <td class="px-4 py-3 text-right">{{ $courier->min_delivery_parcel }}</td>
                <td class="px-4 py-3 text-right whitespace-nowrap">{{ number_format($courier->delivery_charge, 2, '.', ' ') }} ₼</td>
                <td class="px-4 py-3 text-right whitespace-nowrap">{{ number_format($courier->return_charge, 2, '.', ' ') }} ₼</td>
                <td class="px-4 py-3 text-right font-semibold whitespace-nowrap">{{ number_format($courier->currentBalance(), 2, '.', ' ') }} ₼</td>
                <td class="px-4 py-3 text-right whitespace-nowrap">{{ number_format($courier->balance_cash, 2, '.', ' ') }}</td>
                <td class="px-4 py-3 text-right whitespace-nowrap">{{ number_format($courier->balance_pos, 2, '.', ' ') }}</td>
                <td class="px-4 py-3 text-right whitespace-nowrap">{{ number_format($courier->balance_card, 2, '.', ' ') }}</td>
                <td class="px-4 py-3"><x-admin.status-badge :active="$courier->user->status" /></td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-1">
                        @if ($this->can('update') && $courier->device_id)<x-admin.button variant="ghost" size="sm" wire:click="resetDevice({{ $courier->id }})" title="Kuryer hesabı bu cihaza bağlıdır">Cihazı sıfırla</x-admin.button>@endif
                        @if ($this->can('update'))<x-admin.button variant="ghost" size="sm" :href="route('admin.couriers.edit', $courier)" wire:navigate aria-label="Redaktə et"><x-admin.icon name="pencil" class="size-4" /></x-admin.button>@endif
                        @if ($this->can('delete'))<x-admin.button variant="ghost" size="sm" wire:click="confirmDelete({{ $courier->id }})" aria-label="Sil"><x-admin.icon name="trash-2" class="size-4" /></x-admin.button>@endif
                    </div>
                </td>
            </tr>
        @endforeach

        @if ($couriers->hasPages())
            <x-slot:footer>{{ $couriers->links() }}</x-slot:footer>
        @endif
    </x-admin.table>

    <x-admin.confirm-delete />
</div>
