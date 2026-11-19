<form wire:submit="save" class="grid items-start gap-5 xl:grid-cols-[1fr_320px]">
    <div class="flex flex-col gap-5">
        @if ($errors->any())
            <p role="alert" class="rounded-[10px] border border-brand/30 bg-brand-surface px-3 py-2.5 text-sm font-medium text-brand">Yadda saxlanılmadı: {{ $errors->first() }}</p>
        @endif

        <section class="grid gap-4 rounded-2xl border border-line bg-white p-6 md:grid-cols-2">
            <h2 class="text-base font-semibold md:col-span-2">Göndərən</h2>
            <x-admin.field label="Partnyor" for="p-merchant" error="data.merchant_id" required>
                <x-admin.select id="p-merchant" wire:model.live="data.merchant_id" placeholder="Seçin" :invalid="$errors->has('data.merchant_id')">
                    @foreach ($merchants as $m)<option value="{{ $m->id }}">{{ $m->business_name }}</option>@endforeach
                </x-admin.select>
            </x-admin.field>
            <x-admin.field label="Filial (yığma nöqtəsi)" for="p-shop" error="data.merchant_shop_id">
                <x-admin.select id="p-shop" wire:model="data.merchant_shop_id" placeholder="Seçin" :invalid="$errors->has('data.merchant_shop_id')">
                    @foreach ($shops as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                </x-admin.select>
            </x-admin.field>
            <x-admin.field label="Götürən kuryer" for="p-pickup" error="data.pickupman_id">
                <x-admin.select id="p-pickup" wire:model="data.pickupman_id" placeholder="Seçin">
                    @foreach ($couriers as $c)<option value="{{ $c->id }}">{{ $c->user->name }}</option>@endforeach
                </x-admin.select>
            </x-admin.field>
            <x-admin.field label="Partnyorun qaimə nömrəsi" for="p-minv" error="data.merchant_invoice_no">
                <x-admin.input id="p-minv" wire:model="data.merchant_invoice_no" />
            </x-admin.field>
            <x-admin.field label="Göndərənin ünvanı" for="p-paddr" error="data.pickup_address" hint="Boş qalsa, filialın ünvanı götürülür." class="md:col-span-2">
                <x-admin.input id="p-paddr" wire:model="data.pickup_address" />
            </x-admin.field>
        </section>

        <section class="grid gap-4 rounded-2xl border border-line bg-white p-6 md:grid-cols-2">
            <h2 class="text-base font-semibold md:col-span-2">Alıcı</h2>
            <x-admin.field label="Müştəri adı" for="p-cname" error="data.customer_name" required>
                <x-admin.input id="p-cname" wire:model="data.customer_name" :invalid="$errors->has('data.customer_name')" />
            </x-admin.field>
            <x-admin.field label="Telefon" for="p-cphone" error="data.customer_phone">
                <x-admin.input id="p-cphone" wire:model="data.customer_phone" inputmode="tel" :invalid="$errors->has('data.customer_phone')" />
            </x-admin.field>
            <x-admin.field label="Ünvan" for="p-caddr" error="data.customer_address" required class="md:col-span-2">
                <x-admin.input id="p-caddr" wire:model="data.customer_address" :invalid="$errors->has('data.customer_address')" />
            </x-admin.field>
            <x-admin.field label="Enlik (lat)" for="p-lat" error="data.latitude" required>
                <x-admin.input id="p-lat" wire:model="data.latitude" inputmode="decimal" placeholder="40.4093" :invalid="$errors->has('data.latitude')" />
            </x-admin.field>
            <x-admin.field label="Uzunluq (long)" for="p-lng" error="data.longitude" required>
                <x-admin.input id="p-lng" wire:model="data.longitude" inputmode="decimal" placeholder="49.8671" :invalid="$errors->has('data.longitude')" />
            </x-admin.field>
        </section>

        <section class="grid gap-4 rounded-2xl border border-line bg-white p-6 md:grid-cols-3">
            <h2 class="text-base font-semibold md:col-span-3">Bağlama</h2>
            <x-admin.field label="Zona" for="p-zone" error="data.zone_id" required>
                <x-admin.select id="p-zone" wire:model.live="data.zone_id" placeholder="Seçin" :invalid="$errors->has('data.zone_id')">
                    @foreach ($zones as $z)<option value="{{ $z->id }}">{{ $z->name }}</option>@endforeach
                </x-admin.select>
            </x-admin.field>
            <x-admin.field label="Paket növü" for="p-type" error="data.package_type_id" required>
                <x-admin.select id="p-type" wire:model.live="data.package_type_id" placeholder="Seçin" :invalid="$errors->has('data.package_type_id')">
                    @foreach ($packageTypes as $t)<option value="{{ $t->id }}">{{ $t->name }} ({{ $t->weightRange() }})</option>@endforeach
                </x-admin.select>
            </x-admin.field>
            <x-admin.field label="Tarif" for="p-tariff" error="data.tariff_type" required>
                <x-admin.select id="p-tariff" wire:model.live="data.tariff_type" :invalid="$errors->has('data.tariff_type')">
                    @foreach ($tariffs as $t)<option value="{{ $t->value }}">{{ $t->label() }}</option>@endforeach
                </x-admin.select>
            </x-admin.field>
            <x-admin.field label="Paket sayı" for="p-count" error="data.parcel_count" required>
                <x-admin.input id="p-count" wire:model.live.debounce.400ms="data.parcel_count" inputmode="numeric" :invalid="$errors->has('data.parcel_count')" />
            </x-admin.field>
            <x-admin.field label="Çəki (kq)" for="p-weight" error="data.weight" required>
                <x-admin.input id="p-weight" wire:model="data.weight" inputmode="decimal" :invalid="$errors->has('data.weight')" />
            </x-admin.field>
            <x-admin.field label="Tarix" for="p-date" error="data.created_date" required>
                <x-admin.input id="p-date" type="datetime-local" wire:model="data.created_date" :invalid="$errors->has('data.created_date')" />
            </x-admin.field>
            <x-admin.field label="Nağd toplama (AZN)" for="p-cod" error="data.cash_collection">
                <x-admin.input id="p-cod" wire:model.live.debounce.400ms="data.cash_collection" inputmode="decimal" :invalid="$errors->has('data.cash_collection')" />
            </x-admin.field>
            <x-admin.field label="Prioritet" for="p-priority" error="data.priority">
                <x-admin.select id="p-priority" wire:model="data.priority"><option value="normal">Normal</option><option value="high">Yüksək</option></x-admin.select>
            </x-admin.field>
            <div class="flex flex-col justify-end gap-2 pb-1 text-sm">
                <label class="inline-flex items-center gap-2"><input type="checkbox" wire:model="data.fragile_liquid" class="size-4 accent-brand"> Maye / Qırılqan</label>
                <label class="inline-flex items-center gap-2"><input type="checkbox" wire:model.live="data.has_round_trip" class="size-4 accent-brand"> Gediş / Gəliş</label>
            </div>
            <x-admin.field label="Götürmə məntəqəsi" for="p-ppoint" error="data.pickup_point" class="md:col-span-3">
                <x-admin.textarea id="p-ppoint" wire:model="data.pickup_point" rows="2" />
            </x-admin.field>
            <x-admin.field label="Qeyd" for="p-note" error="data.note" class="md:col-span-3">
                <x-admin.textarea id="p-note" wire:model="data.note" rows="2" />
            </x-admin.field>
        </section>
    </div>

    <aside class="sticky top-20 flex flex-col gap-4 rounded-2xl border border-line bg-white p-6">
        <h2 class="text-base font-semibold">Qiymət detalları</h2>
        <dl class="flex flex-col gap-2 text-sm">
            <div class="flex justify-between"><dt class="text-muted">Nağd toplama</dt><dd>{{ number_format((float) $data['cash_collection'], 2) }} ₼</dd></div>
            <div class="flex justify-between"><dt class="text-muted">Çatdırılma qiyməti</dt><dd>{{ $quote ? $quote['price'].' ₼ × '.max(1, (int) $data['parcel_count']) : '—' }}</dd></div>
            @if ($quote)
                <div class="flex justify-between text-xs text-muted"><dt>Mənbə</dt><dd>{{ $quote['source'] === 'merchant' ? 'Partnyorun tarifi' : 'Zona tarifi' }}{{ $quote['delivery_time'] ? ' · '.$quote['delivery_time'] : '' }}</dd></div>
            @endif
            <div class="mt-2 flex justify-between border-t border-line pt-3 text-base font-semibold"><dt>Ümumi qiymət</dt><dd>{{ $total ? $total.' ₼' : '—' }}</dd></div>
        </dl>
        @if (! $quote && $data['merchant_id'] && $data['zone_id'] && $data['package_type_id'])
            <p class="rounded-[10px] bg-amber-surface px-3 py-2 text-xs text-brand">Bu kombinasiya üçün tarif yoxdur — paket yaradıla bilməz.</p>
        @endif
        <x-admin.button type="submit" wire:loading.attr="disabled" wire:target="save">Yadda saxla</x-admin.button>
    </aside>
</form>
