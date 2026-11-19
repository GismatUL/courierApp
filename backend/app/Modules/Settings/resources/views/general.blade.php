<form wire:submit="save" class="flex flex-col gap-5">
    @if ($errors->any())
        <p role="alert" class="rounded-[10px] border border-brand/30 bg-brand-surface px-3 py-2.5 text-sm font-medium text-brand">
            Yadda saxlanılmadı: {{ $errors->count() }} xanada xəta var.
        </p>
    @endif

    <section class="grid gap-4 rounded-2xl border border-line bg-white p-6 md:grid-cols-2">
        <h2 class="text-base font-semibold md:col-span-2">Şirkət</h2>
        <x-admin.field label="Tətbiq adı" for="s-name" error="values.name" required>
            <x-admin.input id="s-name" wire:model="values.name" :invalid="$errors->has('values.name')" />
        </x-admin.field>
        <x-admin.field label="Telefon" for="s-phone" error="values.phone">
            <x-admin.input id="s-phone" wire:model="values.phone" :invalid="$errors->has('values.phone')" />
        </x-admin.field>
        <x-admin.field label="Email" for="s-email" error="values.email">
            <x-admin.input id="s-email" type="email" wire:model="values.email" :invalid="$errors->has('values.email')" />
        </x-admin.field>
        <x-admin.field label="Copyright" for="s-copy" error="values.copyright">
            <x-admin.input id="s-copy" wire:model="values.copyright" :invalid="$errors->has('values.copyright')" />
        </x-admin.field>
        <x-admin.field label="Ünvan" for="s-address" error="values.address" class="md:col-span-2">
            <x-admin.textarea id="s-address" wire:model="values.address" rows="2" />
        </x-admin.field>
    </section>

    <section class="grid gap-4 rounded-2xl border border-line bg-white p-6 md:grid-cols-2">
        <h2 class="text-base font-semibold md:col-span-2">Sifariş və maliyyə</h2>
        <x-admin.field label="Valyuta" for="s-currency" error="values.currency" required>
            <x-admin.select id="s-currency" wire:model="values.currency" :invalid="$errors->has('values.currency')">
                @foreach ($currencies as $currency)
                    <option value="{{ $currency->code }}">{{ $currency->code }} — {{ $currency->name }} ({{ $currency->symbol }})</option>
                @endforeach
            </x-admin.select>
        </x-admin.field>
        <x-admin.field label="Vergi (%)" for="s-tax" error="values.tax">
            <x-admin.input id="s-tax" wire:model="values.tax" inputmode="decimal" :invalid="$errors->has('values.tax')" />
        </x-admin.field>
        <x-admin.field label="İzləmə kodu prefiksi" for="s-track" error="values.par_track_prefix" required hint="Yeni paketlərin izləmə kodu bununla başlayır (məs. KN…).">
            <x-admin.input id="s-track" wire:model.blur="values.par_track_prefix" class="font-mono uppercase" :invalid="$errors->has('values.par_track_prefix')" />
        </x-admin.field>
        <x-admin.field label="Faktura prefiksi" for="s-invoice" error="values.invoice_prefix">
            <x-admin.input id="s-invoice" wire:model.blur="values.invoice_prefix" class="font-mono uppercase" :invalid="$errors->has('values.invoice_prefix')" />
        </x-admin.field>
    </section>

    <section class="grid gap-4 rounded-2xl border border-line bg-white p-6 md:grid-cols-2">
        <h2 class="text-base font-semibold md:col-span-2">Brend</h2>
        @foreach (['logo' => 'Loqo', 'light_logo' => 'Açıq loqo (tünd fon üçün)', 'favicon' => 'Favicon'] as $key => $label)
            <x-admin.field :label="$label" for="s-{{ $key }}" error="files.{{ $key }}" :hint="$key === 'favicon' ? 'PNG və ya ICO, 512 KB-a qədər.' : 'PNG, JPG və ya WEBP, 2 MB-a qədər.'">
                <div class="flex items-center gap-3">
                    <div @class(['grid h-14 w-28 shrink-0 place-items-center overflow-hidden rounded-xl border border-line', 'bg-ink' => $key === 'light_logo', 'bg-surface' => $key !== 'light_logo'])>
                        @if ($files[$key])
                            <img src="{{ $files[$key]->temporaryUrl() }}" alt="" class="max-h-12 max-w-24 object-contain">
                        @elseif ($stored[$key])
                            <img src="{{ Storage::disk('uploads')->url($stored[$key]) }}" alt="{{ $label }}" class="max-h-12 max-w-24 object-contain">
                        @else
                            <span class="text-xs text-muted">Yoxdur</span>
                        @endif
                    </div>
                    <input id="s-{{ $key }}" type="file" wire:model="files.{{ $key }}" accept="{{ $key === 'favicon' ? '.png,.ico' : '.png,.jpg,.jpeg,.webp' }}"
                           class="min-w-0 flex-1 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-brand-surface file:px-3 file:py-2 file:text-sm file:font-semibold file:text-brand">
                    @if ($stored[$key] && ! $files[$key])
                        <x-admin.button variant="ghost" size="sm" wire:click="removeFile('{{ $key }}')" aria-label="{{ $label }} faylını sil"><x-admin.icon name="trash-2" class="size-4" /></x-admin.button>
                    @endif
                </div>
            </x-admin.field>
        @endforeach
        <div class="grid grid-cols-2 gap-4">
            @foreach (['primary_color' => 'Əsas rəng', 'text_color' => 'Mətn rəngi'] as $key => $label)
                <x-admin.field :label="$label" for="s-{{ $key }}" error="values.{{ $key }}" required>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="values.{{ $key }}" class="size-10 shrink-0 cursor-pointer rounded-lg border border-line bg-white p-1" aria-label="{{ $label }} seçin">
                        <x-admin.input id="s-{{ $key }}" wire:model.live.debounce.500ms="values.{{ $key }}" class="font-mono" :invalid="$errors->has('values.'.$key)" />
                    </div>
                </x-admin.field>
            @endforeach
        </div>
    </section>

    <div class="flex justify-end">
        @can('general_settings_update')<x-admin.button type="submit" wire:loading.attr="disabled" wire:target="save,files">Yadda saxla</x-admin.button>@endcan
    </div>
</form>
