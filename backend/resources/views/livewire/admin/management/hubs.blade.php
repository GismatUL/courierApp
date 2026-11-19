<div class="flex flex-col gap-5">
    <x-admin.page-header description="Paketlərin qəbul olunduğu, transfer edildiyi və kuryerlərin bağlı olduğu filiallar.">
        <x-admin.button wire:click="create"><x-admin.icon name="plus" class="size-4" /> Əlavə et</x-admin.button>
    </x-admin.page-header>

    <x-admin.filters>
        <x-admin.field label="Ad" for="f-name" class="w-full sm:w-56">
            <x-admin.input id="f-name" wire:model.live.debounce.300ms="name" placeholder="Axtar…" />
        </x-admin.field>
        <x-admin.field label="Telefon" for="f-phone" class="w-full sm:w-56">
            <x-admin.input id="f-phone" wire:model.live.debounce.300ms="phone" placeholder="Axtar…" />
        </x-admin.field>
    </x-admin.filters>

    <x-admin.table :empty="$hubs->isEmpty()">
        <x-slot:head>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">Ad</th>
            <th class="px-4 py-3">Telefon</th>
            <th class="px-4 py-3">Ünvan</th>
            <th class="px-4 py-3">Əlaqədar şəxs</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Fəaliyyətlər</th>
        </x-slot:head>

        @foreach ($hubs as $hub)
            <tr wire:key="hub-{{ $hub->id }}" class="hover:bg-surface/50">
                <td class="px-4 py-3 text-muted">{{ $hubs->firstItem() + $loop->index }}</td>
                <td class="px-4 py-3 font-semibold">{{ $hub->name }}</td>
                <td class="px-4 py-3 whitespace-nowrap">{{ $hub->phone ?? '—' }}</td>
                <td class="px-4 py-3">
                    {{ $hub->address }}
                    <a href="https://www.google.com/maps?q={{ $hub->latitude }},{{ $hub->longitude }}" target="_blank" rel="noopener noreferrer"
                       class="ml-1 inline-flex items-center gap-0.5 text-xs font-medium text-brand hover:underline">
                        <x-admin.icon name="map-pin" class="size-3.5" />xəritə
                    </a>
                </td>
                <td class="px-4 py-3">{{ $hub->contactUser?->name ?? '—' }}</td>
                <td class="px-4 py-3"><x-admin.status-badge :active="$hub->status" /></td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-1">
                        <x-admin.button variant="ghost" size="sm" wire:click="edit({{ $hub->id }})" aria-label="Redaktə et"><x-admin.icon name="pencil" class="size-4" /></x-admin.button>
                        <x-admin.button variant="ghost" size="sm" wire:click="confirmDelete({{ $hub->id }})" aria-label="Sil"><x-admin.icon name="trash-2" class="size-4" /></x-admin.button>
                    </div>
                </td>
            </tr>
        @endforeach

        @if ($hubs->hasPages())
            <x-slot:footer>{{ $hubs->links() }}</x-slot:footer>
        @endif
    </x-admin.table>

    <x-admin.modal wire:model="showForm" :title="$form->id ? 'Filialı redaktə et' : 'Yeni filial'">
        <form wire:submit="save" class="grid gap-4 sm:grid-cols-2">
            <x-admin.field label="Ad" for="hub-name" error="form.name" required class="sm:col-span-2">
                <x-admin.input id="hub-name" wire:model="form.name" :invalid="$errors->has('form.name')" />
            </x-admin.field>
            <x-admin.field label="Telefon" for="hub-phone" error="form.phone">
                <x-admin.input id="hub-phone" wire:model="form.phone" placeholder="+994 12 400 00 00" :invalid="$errors->has('form.phone')" />
            </x-admin.field>
            <x-admin.field label="Əlaqədar şəxs" for="hub-contact" error="form.contact_user_id">
                <x-admin.select id="hub-contact" wire:model="form.contact_user_id" placeholder="Seçin">
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </x-admin.select>
            </x-admin.field>
            <x-admin.field label="Ünvan" for="hub-address" error="form.address" required class="sm:col-span-2">
                <x-admin.input id="hub-address" wire:model="form.address" :invalid="$errors->has('form.address')" />
            </x-admin.field>
            <x-admin.field label="Enlik (lat)" for="hub-lat" error="form.latitude" required>
                <x-admin.input id="hub-lat" wire:model="form.latitude" inputmode="decimal" placeholder="40.4093" :invalid="$errors->has('form.latitude')" />
            </x-admin.field>
            <x-admin.field label="Uzunluq (long)" for="hub-lng" error="form.longitude" required>
                <x-admin.input id="hub-lng" wire:model="form.longitude" inputmode="decimal" placeholder="49.8671" :invalid="$errors->has('form.longitude')" />
            </x-admin.field>
            <p class="text-xs text-muted sm:col-span-2">Koordinatları Google Maps-də nöqtənin üzərinə sağ kliklə kopyalaya bilərsiniz. Xəritədən seçim Google Xəritə açarı əlavə edildikdən sonra qoşulacaq.</p>
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
