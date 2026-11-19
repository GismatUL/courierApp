<div class="flex flex-col gap-5">
    <x-admin.page-header description="Məzənnə 1 ABŞ dollarına nisbətdə göstərilir. Əsas valyuta Ümumi Tənzimləmələrdə seçilir.">
        @if ($this->can('create'))<x-admin.button wire:click="create"><x-admin.icon name="plus" class="size-4" /> Əlavə et</x-admin.button>@endif
    </x-admin.page-header>

    <x-admin.table :empty="$currencies->isEmpty()">
        <x-slot:head>
            <th class="px-4 py-3">Ad</th>
            <th class="px-4 py-3">Kod</th>
            <th class="px-4 py-3">Simvol</th>
            <th class="px-4 py-3">Mübadilə məzənnəsi</th>
            <th class="px-4 py-3">Nümunə</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Fəaliyyətlər</th>
        </x-slot:head>

        @foreach ($currencies as $currency)
            <tr wire:key="currency-{{ $currency->id }}" class="hover:bg-surface/50">
                <td class="px-4 py-3">
                    <span class="font-semibold">{{ $currency->name }}</span>
                    @if ($currency->code === $default)
                        <span class="ml-1.5 rounded-full bg-brand-surface px-2 py-0.5 text-xs font-semibold text-brand">Əsas</span>
                    @endif
                </td>
                <td class="px-4 py-3 font-mono">{{ $currency->code }}</td>
                <td class="px-4 py-3">{{ $currency->symbol }}</td>
                <td class="px-4 py-3 whitespace-nowrap">1 USD = {{ rtrim(rtrim($currency->exchange_rate, '0'), '.') }} {{ $currency->code }}</td>
                <td class="px-4 py-3 whitespace-nowrap">{{ $currency->format(1250.5) }}</td>
                <td class="px-4 py-3"><x-admin.status-badge :active="$currency->status" /></td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-1">
                        @if ($this->can('update'))<x-admin.button variant="ghost" size="sm" wire:click="edit({{ $currency->id }})" aria-label="Redaktə et"><x-admin.icon name="pencil" class="size-4" /></x-admin.button>@endif
                        @if ($this->can('delete'))<x-admin.button variant="ghost" size="sm" wire:click="confirmDelete({{ $currency->id }})" aria-label="Sil"><x-admin.icon name="trash-2" class="size-4" /></x-admin.button>@endif
                    </div>
                </td>
            </tr>
        @endforeach
    </x-admin.table>

    <x-admin.modal wire:model="showForm" :title="$form->id ? 'Valyutanı redaktə et' : 'Yeni valyuta'">
        <form wire:submit="save" class="grid gap-4 sm:grid-cols-2">
            <x-admin.field label="Ad" for="cur-name" error="form.name" required class="sm:col-span-2">
                <x-admin.input id="cur-name" wire:model="form.name" placeholder="Azərbaycan manatı" :invalid="$errors->has('form.name')" />
            </x-admin.field>
            <x-admin.field label="Kod" for="cur-code" error="form.code" required>
                <x-admin.input id="cur-code" wire:model="form.code" maxlength="3" class="font-mono uppercase" placeholder="AZN" :invalid="$errors->has('form.code')" />
            </x-admin.field>
            <x-admin.field label="Simvol" for="cur-symbol" error="form.symbol" required>
                <x-admin.input id="cur-symbol" wire:model="form.symbol" placeholder="₼" :invalid="$errors->has('form.symbol')" />
            </x-admin.field>
            <x-admin.field label="1 USD = ?" for="cur-rate" error="form.exchange_rate" required>
                <x-admin.input id="cur-rate" wire:model="form.exchange_rate" inputmode="decimal" placeholder="1.70" :invalid="$errors->has('form.exchange_rate')" />
            </x-admin.field>
            <x-admin.field label="Simvolun yeri" for="cur-pos" error="form.position" required>
                <x-admin.select id="cur-pos" wire:model="form.position">
                    <option value="after">Məbləğdən sonra (10.00 ₼)</option>
                    <option value="before">Məbləğdən əvvəl ($10.00)</option>
                </x-admin.select>
            </x-admin.field>
            <div class="sm:col-span-2">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" wire:model="form.status" class="size-4 accent-brand"> Aktiv
                </label>
                @error('form.status')<p class="mt-1 text-xs font-medium text-brand" role="alert">{{ $message }}</p>@enderror
            </div>
            <div class="flex justify-end gap-2 sm:col-span-2">
                <x-admin.button variant="secondary" @click="show = false">İmtina</x-admin.button>
                <x-admin.button type="submit" wire:loading.attr="disabled" wire:target="save">Yadda saxla</x-admin.button>
            </div>
        </form>
    </x-admin.modal>

    <x-admin.confirm-delete />
</div>
