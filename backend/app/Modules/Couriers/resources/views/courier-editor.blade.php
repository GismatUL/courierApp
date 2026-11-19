<form wire:submit="save" class="flex flex-col gap-5">
    <nav class="text-sm text-muted" aria-label="Breadcrumb">
        <a href="{{ route('admin.couriers.index') }}" wire:navigate class="hover:text-ink">Kuryerlər</a>
        <span class="mx-1.5">/</span>
        <span class="font-medium text-ink">{{ $courier?->user->name ?? 'Yeni kuryer' }}</span>
    </nav>

    @if ($errors->any())
        <p role="alert" class="rounded-[10px] border border-brand/30 bg-brand-surface px-3 py-2.5 text-sm font-medium text-brand">Yadda saxlanılmadı: {{ $errors->count() }} xanada xəta var.</p>
    @endif

    <section class="grid gap-4 rounded-2xl border border-line bg-white p-6 md:grid-cols-2">
        <div class="md:col-span-2">
            <h2 class="text-base font-semibold">Mobil tətbiqə giriş</h2>
            <p class="text-sm text-muted">Kuryer bu email və şifrə ilə yalnız mobil tətbiqə daxil olur; admin panelə girişi yoxdur.</p>
        </div>
        <x-admin.field label="Ad" for="c-name" error="account.name" required>
            <x-admin.input id="c-name" wire:model="account.name" :invalid="$errors->has('account.name')" />
        </x-admin.field>
        <x-admin.field label="Email" for="c-email" error="account.email" required>
            <x-admin.input id="c-email" type="email" wire:model="account.email" autocomplete="off" :invalid="$errors->has('account.email')" />
        </x-admin.field>
        <x-admin.field label="Telefon" for="c-mobile" error="account.mobile" required hint="994XXXXXXXXX">
            <x-admin.input id="c-mobile" wire:model="account.mobile" inputmode="tel" :invalid="$errors->has('account.mobile')" />
        </x-admin.field>
        <x-admin.field label="Şifrə" for="c-password" error="account.password" :required="! $courier" :hint="$courier ? 'Boş qalsa, dəyişmir.' : '10–72 simvol.'">
            <x-admin.input id="c-password" type="password" wire:model="account.password" autocomplete="new-password" :invalid="$errors->has('account.password')" />
        </x-admin.field>
    </section>

    <section class="grid gap-4 rounded-2xl border border-line bg-white p-6 md:grid-cols-3">
        <h2 class="text-base font-semibold md:col-span-3">İş</h2>
        <x-admin.field label="Mərkəz" for="c-hub" error="profile.hub_id" required>
            <x-admin.select id="c-hub" wire:model="profile.hub_id" placeholder="Seçin" :invalid="$errors->has('profile.hub_id')">
                @foreach ($hubs as $hub)<option value="{{ $hub->id }}">{{ $hub->name }}</option>@endforeach
            </x-admin.select>
        </x-admin.field>
        <x-admin.field label="Kuryer növü" for="c-type" error="profile.type" required>
            <x-admin.select id="c-type" wire:model="profile.type">
                @foreach ($types as $type)<option value="{{ $type->value }}">{{ $type->label() }}</option>@endforeach
            </x-admin.select>
        </x-admin.field>
        <x-admin.field label="Nəqliyyat növü" for="c-car" error="profile.car_type" required hint="Canlı xəritədə ikon bununla seçilir.">
            <x-admin.select id="c-car" wire:model="profile.car_type">
                @foreach ($carTypes as $car)<option value="{{ $car->value }}">{{ $car->label() }}</option>@endforeach
            </x-admin.select>
        </x-admin.field>
        <x-admin.field label="Maşın nömrəsi" for="c-plate" error="profile.car_number">
            <x-admin.input id="c-plate" wire:model="profile.car_number" class="uppercase" placeholder="10-AA-123" />
        </x-admin.field>
        <x-admin.field label="VÖEN" for="c-voen" error="profile.voen">
            <x-admin.input id="c-voen" wire:model="profile.voen" inputmode="numeric" :invalid="$errors->has('profile.voen')" />
        </x-admin.field>
        <x-admin.field label="Status" for="c-status" error="account.status" required>
            <x-admin.select id="c-status" wire:model="account.status"><option value="1">Aktiv</option><option value="0">Passiv</option></x-admin.select>
        </x-admin.field>
        <x-admin.field label="Minimum çatdırılma paketi" for="c-min-d" error="profile.min_delivery_parcel" required>
            <x-admin.input id="c-min-d" wire:model="profile.min_delivery_parcel" inputmode="numeric" :invalid="$errors->has('profile.min_delivery_parcel')" />
        </x-admin.field>
        <x-admin.field label="Minimum yığma paketi" for="c-min-p" error="profile.min_pickup_parcel">
            <x-admin.input id="c-min-p" wire:model="profile.min_pickup_parcel" inputmode="numeric" :invalid="$errors->has('profile.min_pickup_parcel')" />
        </x-admin.field>
        <x-admin.field label="İş günü" for="c-days" error="profile.work_days">
            <x-admin.input id="c-days" wire:model="profile.work_days" inputmode="numeric" :invalid="$errors->has('profile.work_days')" />
        </x-admin.field>
    </section>

    <section class="grid gap-4 rounded-2xl border border-line bg-white p-6 md:grid-cols-3">
        <h2 class="text-base font-semibold md:col-span-3">Haqlar və maaş (AZN)</h2>
        <x-admin.field label="Çatdırılma haqqı" for="c-fee-d" error="profile.delivery_charge" required hint="Hər çatdırılmaya görə.">
            <x-admin.input id="c-fee-d" wire:model="profile.delivery_charge" inputmode="decimal" :invalid="$errors->has('profile.delivery_charge')" />
        </x-admin.field>
        <x-admin.field label="Yığma haqqı" for="c-fee-p" error="profile.pickup_charge" required>
            <x-admin.input id="c-fee-p" wire:model="profile.pickup_charge" inputmode="decimal" :invalid="$errors->has('profile.pickup_charge')" />
        </x-admin.field>
        <x-admin.field label="Geri qaytarma haqqı" for="c-fee-r" error="profile.return_charge" required>
            <x-admin.input id="c-fee-r" wire:model="profile.return_charge" inputmode="decimal" :invalid="$errors->has('profile.return_charge')" />
        </x-admin.field>
        <x-admin.field label="Maaş" for="c-salary" error="profile.salary" required>
            <x-admin.input id="c-salary" wire:model="profile.salary" inputmode="decimal" :invalid="$errors->has('profile.salary')" />
        </x-admin.field>
        <x-admin.field label="Bonus növü" for="c-bonus" error="profile.bonus_type">
            <x-admin.select id="c-bonus" wire:model="profile.bonus_type" placeholder="Yoxdur">
                @foreach ($bonusTypes as $bonus)<option value="{{ $bonus->value }}">{{ $bonus->label() }}</option>@endforeach
            </x-admin.select>
        </x-admin.field>
        <x-admin.field label="Açılış balansı (nağd)" for="c-opening" error="profile.opening_balance" :required="! $courier" :hint="$courier ? 'Yaradıldıqdan sonra dəyişdirilmir.' : 'Kuryerin üzərindəki nağd məbləğ.'">
            <x-admin.input id="c-opening" wire:model="profile.opening_balance" inputmode="decimal" :disabled="(bool) $courier" :invalid="$errors->has('profile.opening_balance')" />
        </x-admin.field>
    </section>

    <section class="grid gap-4 rounded-2xl border border-line bg-white p-6 md:grid-cols-3">
        <h2 class="text-base font-semibold md:col-span-3">Ünvan və sənədlər</h2>
        <x-admin.field label="Ünvan" for="c-address" error="profile.address" class="md:col-span-3">
            <x-admin.input id="c-address" wire:model="profile.address" />
        </x-admin.field>
        <x-admin.field label="Enlik (lat)" for="c-lat" error="profile.latitude">
            <x-admin.input id="c-lat" wire:model="profile.latitude" inputmode="decimal" placeholder="40.4093" :invalid="$errors->has('profile.latitude')" />
        </x-admin.field>
        <x-admin.field label="Uzunluq (long)" for="c-lng" error="profile.longitude">
            <x-admin.input id="c-lng" wire:model="profile.longitude" inputmode="decimal" placeholder="49.8671" :invalid="$errors->has('profile.longitude')" />
        </x-admin.field>
        <div></div>
        @foreach (\App\Modules\Couriers\Models\Courier::FILES as $column => $label)
            <x-admin.field :label="$label" for="c-{{ $column }}" error="files.{{ $column }}" :hint="$column === 'image_file' ? 'JPG, PNG, WEBP · 2 MB' : 'PDF, JPG, PNG · 5 MB'">
                <input id="c-{{ $column }}" type="file" wire:model="files.{{ $column }}" accept="{{ $column === 'image_file' ? '.jpg,.jpeg,.png,.webp' : '.pdf,.jpg,.jpeg,.png' }}"
                       class="text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-brand-surface file:px-3 file:py-2 file:text-sm file:font-semibold file:text-brand">
                @if ($courier?->{$column})
                    <a href="{{ route('admin.couriers.file', [$courier, $column]) }}" target="_blank" class="text-xs font-medium text-brand hover:underline">Mövcud faylı aç</a>
                @endif
            </x-admin.field>
        @endforeach
    </section>

    <div class="flex justify-end gap-2">
        <x-admin.button variant="secondary" :href="route('admin.couriers.index')" wire:navigate>İmtina</x-admin.button>
        <x-admin.button type="submit" wire:loading.attr="disabled" wire:target="save,files">Yadda saxla</x-admin.button>
    </div>
</form>
