<form wire:submit="save" class="flex flex-col gap-5">
    <nav class="text-sm text-muted" aria-label="Breadcrumb">
        <a href="{{ route('admin.merchants.index') }}" wire:navigate class="hover:text-ink">Partnyorlar</a>
        <span class="mx-1.5">/</span>
        <span class="font-medium text-ink">{{ $merchant?->business_name ?? 'Yeni partnyor' }}</span>
        @if ($merchant)<span class="ml-2 rounded-full bg-surface px-2 py-0.5 font-mono text-xs">{{ $merchant->unique_id }}</span>@endif
    </nav>

    @if ($errors->any())
        <p role="alert" class="rounded-[10px] border border-brand/30 bg-brand-surface px-3 py-2.5 text-sm font-medium text-brand">
            Yadda saxlanılmadı: {{ $errors->count() }} xanada xəta var.
        </p>
    @endif

    <section class="grid gap-4 rounded-2xl border border-line bg-white p-6 md:grid-cols-2">
        <h2 class="text-base font-semibold md:col-span-2">Şirkət</h2>
        <x-admin.field label="Biznes adı" for="m-business" error="data.business_name" required>
            <x-admin.input id="m-business" wire:model="data.business_name" :invalid="$errors->has('data.business_name')" />
        </x-admin.field>
        <x-admin.field label="Partnyor növü" for="m-type" error="data.merchant_type_id">
            <x-admin.select id="m-type" wire:model="data.merchant_type_id" placeholder="Seçin">
                @foreach ($types as $type)
                    <option value="{{ $type->id }}">{{ $type->title }}</option>
                @endforeach
            </x-admin.select>
        </x-admin.field>
        <x-admin.field label="Ünvan" for="m-address" error="data.address" class="md:col-span-2">
            <x-admin.textarea id="m-address" wire:model="data.address" rows="2" />
        </x-admin.field>
        <fieldset class="md:col-span-2">
            <legend class="mb-1.5 text-xs font-semibold uppercase text-muted">Xidmət edən filiallar <span class="text-brand">*</span></legend>
            <div class="flex flex-wrap gap-x-4 gap-y-2">
                @forelse ($hubs as $hub)
                    <label class="inline-flex items-center gap-1.5 text-sm"><input type="checkbox" value="{{ $hub->id }}" wire:model="hubIds" class="size-4 accent-brand"> {{ $hub->name }}</label>
                @empty
                    <p class="text-sm text-muted">Əvvəlcə <a href="{{ route('admin.hubs.index') }}" class="font-medium text-brand hover:underline">filial</a> əlavə edin.</p>
                @endforelse
            </div>
            @error('hubIds')<p class="mt-1 text-xs font-medium text-brand" role="alert">{{ $message }}</p>@enderror
        </fieldset>
    </section>

    <section class="grid gap-4 rounded-2xl border border-line bg-white p-6 md:grid-cols-2">
        <div class="md:col-span-2">
            <h2 class="text-base font-semibold">Partnyor istifadəçisi</h2>
            <p class="text-sm text-muted">Partnyor portalına giriş üçün. Şifrə boş qalsa, giriş olmayacaq{{ $merchant ? ' (və ya mövcud şifrə dəyişməyəcək)' : '' }}.</p>
        </div>
        <x-admin.field label="Ad" for="m-contact" error="data.contact_name">
            <x-admin.input id="m-contact" wire:model="data.contact_name" :invalid="$errors->has('data.contact_name')" />
        </x-admin.field>
        <x-admin.field label="Email (giriş)" for="m-email" error="data.email">
            <x-admin.input id="m-email" type="email" wire:model="data.email" autocomplete="off" :invalid="$errors->has('data.email')" />
        </x-admin.field>
        <x-admin.field label="Telefon" for="m-mobile" error="data.mobile" hint="994XXXXXXXXX">
            <x-admin.input id="m-mobile" wire:model="data.mobile" inputmode="tel" :invalid="$errors->has('data.mobile')" />
        </x-admin.field>
        <x-admin.field label="Şifrə" for="m-password" error="data.password" hint="10–72 simvol.">
            <x-admin.input id="m-password" type="password" wire:model="data.password" autocomplete="new-password" :invalid="$errors->has('data.password')" />
        </x-admin.field>
    </section>

    <section class="grid gap-4 rounded-2xl border border-line bg-white p-6 md:grid-cols-3">
        <h2 class="text-base font-semibold md:col-span-3">Maliyyə şərtləri</h2>
        <x-admin.field label="Açılış balansı (AZN)" for="m-opening" error="data.opening_balance" :required="! $merchant" :hint="$merchant ? 'Yaradıldıqdan sonra dəyişdirilmir.' : null">
            <x-admin.input id="m-opening" wire:model="data.opening_balance" inputmode="decimal" :disabled="(bool) $merchant" :invalid="$errors->has('data.opening_balance')" />
        </x-admin.field>
        <x-admin.field label="ƏDV (%)" for="m-vat" error="data.vat" required>
            <x-admin.input id="m-vat" wire:model="data.vat" inputmode="decimal" :invalid="$errors->has('data.vat')" />
        </x-admin.field>
        <x-admin.field label="Geri qaytarma haqqı (%)" for="m-return" error="data.return_charge_pct" required>
            <x-admin.input id="m-return" wire:model="data.return_charge_pct" inputmode="decimal" :invalid="$errors->has('data.return_charge_pct')" />
        </x-admin.field>
        <x-admin.field label="Nağd toplama haqqı: şəhər daxili (%)" for="m-cod-in" error="data.cod_charge_inside_city" required>
            <x-admin.input id="m-cod-in" wire:model="data.cod_charge_inside_city" inputmode="decimal" :invalid="$errors->has('data.cod_charge_inside_city')" />
        </x-admin.field>
        <x-admin.field label="Ətraf (%)" for="m-cod-sub" error="data.cod_charge_sub_city" required>
            <x-admin.input id="m-cod-sub" wire:model="data.cod_charge_sub_city" inputmode="decimal" :invalid="$errors->has('data.cod_charge_sub_city')" />
        </x-admin.field>
        <x-admin.field label="Şəhərkənarı (%)" for="m-cod-out" error="data.cod_charge_outside_city" required>
            <x-admin.input id="m-cod-out" wire:model="data.cod_charge_outside_city" inputmode="decimal" :invalid="$errors->has('data.cod_charge_outside_city')" />
        </x-admin.field>
        <x-admin.field label="Ödəmə müddəti (gün)" for="m-period" error="data.payment_period_days">
            <x-admin.input id="m-period" wire:model="data.payment_period_days" inputmode="numeric" :invalid="$errors->has('data.payment_period_days')" />
        </x-admin.field>
        <x-admin.field label="Cüzdan istifadəsi" for="m-wallet" error="data.wallet_enabled">
            <x-admin.select id="m-wallet" wire:model="data.wallet_enabled"><option value="0">Passiv</option><option value="1">Aktiv</option></x-admin.select>
        </x-admin.field>
        <x-admin.field label="Məcburi OTP kod" for="m-otp" error="data.otp_required" required>
            <x-admin.select id="m-otp" wire:model="data.otp_required"><option value="0">Passiv</option><option value="1">Aktiv</option></x-admin.select>
        </x-admin.field>
    </section>

    <section class="grid gap-4 rounded-2xl border border-line bg-white p-6 md:grid-cols-3">
        <h2 class="text-base font-semibold md:col-span-3">Sənədlər və istinad</h2>
        @foreach (\App\Modules\Merchants\Models\Merchant::FILES as $column => $label)
            <x-admin.field :label="$label" for="m-{{ $column }}" error="files.{{ $column }}" :hint="$column === 'image_file' ? 'JPG, PNG, WEBP · 2 MB' : 'PDF, JPG, PNG · 5 MB'">
                <input id="m-{{ $column }}" type="file" wire:model="files.{{ $column }}" accept="{{ $column === 'image_file' ? '.jpg,.jpeg,.png,.webp' : '.pdf,.jpg,.jpeg,.png' }}"
                       class="text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-brand-surface file:px-3 file:py-2 file:text-sm file:font-semibold file:text-brand">
                @if ($merchant?->{$column})
                    <a href="{{ route('admin.merchants.file', [$merchant, $column]) }}" target="_blank" class="text-xs font-medium text-brand hover:underline">Mövcud faylı aç</a>
                @endif
            </x-admin.field>
        @endforeach
        <x-admin.field label="İstinad şəxs" for="m-ref-name" error="data.reference_name">
            <x-admin.input id="m-ref-name" wire:model="data.reference_name" />
        </x-admin.field>
        <x-admin.field label="İstinad telefonu" for="m-ref-phone" error="data.reference_phone" hint="994XXXXXXXXX">
            <x-admin.input id="m-ref-phone" wire:model="data.reference_phone" inputmode="tel" :invalid="$errors->has('data.reference_phone')" />
        </x-admin.field>
        <x-admin.field label="Status" for="m-status" error="data.status" required>
            <x-admin.select id="m-status" wire:model="data.status"><option value="1">Aktiv</option><option value="0">Passiv</option></x-admin.select>
        </x-admin.field>
    </section>

    <div class="flex justify-end gap-2">
        <x-admin.button variant="secondary" :href="$merchant ? route('admin.merchants.show', $merchant) : route('admin.merchants.index')" wire:navigate>İmtina</x-admin.button>
        <x-admin.button type="submit" wire:loading.attr="disabled" wire:target="save,files">Yadda saxla</x-admin.button>
    </div>
</form>
