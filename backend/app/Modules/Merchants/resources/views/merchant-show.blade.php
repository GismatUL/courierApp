<div class="flex flex-col gap-5">
    <nav class="text-sm text-muted" aria-label="Breadcrumb">
        <a href="{{ route('admin.merchants.index') }}" wire:navigate class="hover:text-ink">Partnyorlar</a>
        <span class="mx-1.5">/</span>
        <span class="font-medium text-ink">{{ $merchant->business_name }}</span>
    </nav>

    <section class="flex flex-wrap items-center gap-4 rounded-2xl border border-line bg-white p-6">
        @if ($merchant->image_file)
            <img src="{{ route('admin.merchants.file', [$merchant, 'image_file']) }}" alt="" class="size-14 rounded-xl border border-line object-contain">
        @else
            <span class="grid size-14 place-items-center rounded-xl bg-brand-surface text-lg font-bold text-brand">{{ mb_strtoupper(mb_substr($merchant->business_name, 0, 1)) }}</span>
        @endif
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <h2 class="text-lg font-semibold">{{ $merchant->business_name }}</h2>
                <span class="rounded-full bg-surface px-2 py-0.5 font-mono text-xs">{{ $merchant->unique_id }}</span>
                <x-admin.status-badge :active="$merchant->status" />
            </div>
            <p class="text-sm text-muted">{{ collect([$merchant->contact_name, $merchant->email, $merchant->mobile])->filter()->implode(' · ') ?: '—' }}</p>
        </div>
        @can('merchant_update')
            <x-admin.button variant="secondary" :href="route('admin.merchants.edit', $merchant)" wire:navigate><x-admin.icon name="pencil" class="size-4" /> Redaktə et</x-admin.button>
        @endcan
    </section>

    <section class="grid gap-4 sm:grid-cols-3" aria-label="Göstəricilər">
        @foreach (['Paket sayı' => '—', 'Məbləğ' => '—', 'Cari balans' => number_format($merchant->current_balance, 2, '.', ' ').' ₼'] as $label => $value)
            <div class="rounded-2xl border border-line bg-white p-5">
                <span class="text-xs font-semibold uppercase text-muted">{{ $label }}</span>
                <p class="mt-1 text-2xl font-bold">{{ $value }}</p>
            </div>
        @endforeach
    </section>

    <div class="flex gap-1 overflow-x-auto border-b border-line" role="tablist">
        @foreach ($tabs as $key => $item)
            <button type="button" role="tab" wire:click="$set('tab', '{{ $key }}')" aria-selected="{{ $tab === $key ? 'true' : 'false' }}"
                    @class(['-mb-px whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-semibold', 'border-brand text-brand' => $tab === $key, 'border-transparent text-muted hover:text-ink' => $tab !== $key])>
                {{ $item['label'] }}
            </button>
        @endforeach
    </div>

    <div role="tabpanel">
        @switch($tab)
            @case('charges')
                <livewire:merchants-charges :merchant-id="$merchant->id" :key="'charges-'.$merchant->id" />
                @break
            @case('shops')
                <livewire:merchants-shops :merchant-id="$merchant->id" :key="'shops-'.$merchant->id" />
                @break
            @case('payments')
                <livewire:merchants-payment-accounts :merchant-id="$merchant->id" :key="'payments-'.$merchant->id" />
                @break
            @case('invoices')
                <p class="rounded-2xl border border-line bg-white px-6 py-10 text-center text-sm text-muted">Fakturalar Maliyyə modulu ilə əlavə olunacaq (çatdırılmış paketlər üzrə hesablanır).</p>
                @break
            @default
                <dl class="grid gap-x-6 gap-y-4 rounded-2xl border border-line bg-white p-6 text-sm sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ([
                        'Biznes adı' => $merchant->business_name,
                        'Partnyor növü' => $merchant->type?->title,
                        'Mərkəz' => $merchant->hubs->pluck('name')->implode(', '),
                        'Unikal ID' => $merchant->unique_id,
                        'Açılış balansı' => number_format($merchant->opening_balance, 2, '.', ' ').' ₼',
                        'ƏDV' => (float) $merchant->vat.'%',
                        'Geri qaytarma haqqı' => (float) $merchant->return_charge_pct.'%',
                        'Nağd toplama haqqı' => (float) $merchant->cod_charge_inside_city.'% / '.(float) $merchant->cod_charge_sub_city.'% / '.(float) $merchant->cod_charge_outside_city.'%',
                        'Ödəmə müddəti' => $merchant->payment_period_days !== null ? $merchant->payment_period_days.' gün' : null,
                        'Cüzdan / OTP' => ($merchant->wallet_enabled ? 'Cüzdan aktiv' : 'Cüzdan passiv').' · '.($merchant->otp_required ? 'OTP məcburi' : 'OTP yox'),
                        'Əsas yığma nöqtəsi' => $merchant->defaultShop?->name,
                        'İstinad' => collect([$merchant->reference_name, $merchant->reference_phone])->filter()->implode(' · '),
                        'Ünvan' => $merchant->address,
                    ] as $label => $value)
                        <div>
                            <dt class="text-xs font-semibold uppercase text-muted">{{ $label }}</dt>
                            <dd class="mt-0.5">{{ filled($value) ? $value : '—' }}</dd>
                        </div>
                    @endforeach
                    <div class="sm:col-span-2 lg:col-span-3">
                        <dt class="text-xs font-semibold uppercase text-muted">Sənədlər</dt>
                        <dd class="mt-1 flex flex-wrap gap-2">
                            @forelse (array_filter(\App\Modules\Merchants\Models\Merchant::FILES, fn ($c) => $merchant->{$c}, ARRAY_FILTER_USE_KEY) as $column => $label)
                                <a href="{{ route('admin.merchants.file', [$merchant, $column]) }}" target="_blank" class="rounded-lg bg-brand-surface px-3 py-1.5 text-xs font-semibold text-brand hover:underline">{{ $label }}</a>
                            @empty
                                <span>—</span>
                            @endforelse
                        </dd>
                    </div>
                </dl>
        @endswitch
    </div>
</div>
