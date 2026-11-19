<div class="flex flex-col gap-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <nav class="text-sm text-muted" aria-label="Breadcrumb">
            <a href="{{ route('admin.parcels.index') }}" wire:navigate class="hover:text-ink">Paketlər</a>
            <span class="mx-1.5">/</span>
            <span class="font-mono font-medium text-ink">{{ $parcel->tracking_id }}</span>
            <span class="ml-2 text-xs">Faktura: #{{ $parcel->invoice_no }}</span>
        </nav>
        <div class="flex flex-wrap gap-2">
            @if ($canAct)<x-admin.button wire:click="$dispatch('parcel-action', { parcelId: {{ $parcel->id }} })">Status dəyiş</x-admin.button>@endif
            <x-admin.button variant="secondary" :href="route('admin.parcels.labels', ['ids' => $parcel->id])" target="_blank"><x-admin.icon name="printer" class="size-4" /> Etiket</x-admin.button>
            @can('parcel_update')
                @if ($parcel->status->value === 1)<x-admin.button variant="secondary" :href="route('admin.parcels.edit', $parcel)" wire:navigate><x-admin.icon name="pencil" class="size-4" /> Redaktə et</x-admin.button>@endif
                @if (Route::has('admin.parcels.address-link'))
                    <x-admin.button variant="secondary" :href="route('admin.parcels.address-link', $parcel)" target="_blank" title="Ünvan təsdiqi səhifəsi (müştəriyə SMS ilə gedən link)">Change Location</x-admin.button>
                @endif
            @endcan
        </div>
    </div>

    {{-- 5-step progress (spec §4.4) --}}
    <ol class="grid grid-cols-5 gap-2 rounded-2xl border border-line bg-white p-4 text-center text-xs font-semibold">
        @foreach ($stages as $i => $stage)
            @php($reached = $parcel->status->stage() >= $i + 1)
            <li @class(['flex flex-col items-center gap-1.5', 'text-brand' => $reached, 'text-muted' => ! $reached])>
                <span @class(['grid size-7 place-items-center rounded-full', 'bg-brand text-white' => $reached, 'bg-surface' => ! $reached])>{{ $i + 1 }}</span>
                {{ $stage }}
            </li>
        @endforeach
    </ol>

    <div class="grid gap-5 lg:grid-cols-2">
        @foreach ([
            'Paket məlumatı' => [
                'Status' => $parcel->status->label().($parcel->rejectReason ? ' — '.$parcel->rejectReason->name : ''),
                'Qaimə №' => $parcel->invoice_no.($parcel->merchant_invoice_no ? ' / '.$parcel->merchant_invoice_no : ''),
                'Çəki' => (float) $parcel->weight.' kq · '.$parcel->parcel_count.' ədəd',
                'Maye / qırılqan' => $parcel->fragile_liquid ? 'Bəli' : 'Xeyr',
                'Prioritet' => $parcel->priority === 'high' ? 'Yüksək' : 'Normal',
                'Mərkəz / qutu' => collect([$parcel->hub?->name, $parcel->shelf ? $parcel->shelf->name.' ('.$parcel->shelf->code.')' : null])->filter()->implode(' · '),
                'Yaradan' => $parcel->creator?->name,
                'Qeyd' => $parcel->note,
            ],
            'Çatdırılma məlumatı' => [
                'Tarif / paket növü' => $parcel->tariff_type->label().' · '.$parcel->packageType->name,
                'Zona' => $parcel->zone->name,
                'Çatdırılma qiyməti' => $parcel->delivery_charge.' ₼ × '.$parcel->parcel_count,
                'Nağd toplama' => $parcel->cash_collection.' ₼',
                'Ümumi qiymət' => $parcel->total.' ₼',
                'Kuryer' => $parcel->deliveryMan?->user->name,
                'Götürən kuryer' => $parcel->pickupman?->user->name,
                'Gediş / gəliş' => $parcel->has_round_trip ? 'Bəli' : 'Xeyr',
            ],
            'Göndərici' => [
                'Biznes adı' => $parcel->merchant->business_name,
                'Mobil' => $parcel->merchant->mobile,
                'Email' => $parcel->merchant->email,
                'Filial' => $parcel->shop?->name,
                'Götürmə ünvanı' => $parcel->pickup_address,
            ],
            'Alıcı' => [
                'Ad' => $parcel->customer_name,
                'Telefon' => $parcel->customer_phone,
                'Ünvan' => $parcel->customer_address,
                'Xəritə' => $parcel->map_status === 1 ? 'Müştəri təsdiqləyib' : 'Təsdiqlənməyib',
            ],
        ] as $title => $rows)
            <section class="rounded-2xl border border-line bg-white p-6">
                <h2 class="mb-3 text-base font-semibold">{{ $title }}</h2>
                <dl class="grid grid-cols-[150px_1fr] gap-x-4 gap-y-2 text-sm">
                    @foreach ($rows as $label => $value)
                        <dt class="text-muted">{{ $label }}</dt><dd>{{ filled($value) ? $value : '—' }}</dd>
                    @endforeach
                    @if ($title === 'Alıcı')
                        <dt class="text-muted">Koordinat</dt>
                        <dd><a href="https://www.google.com/maps?q={{ $parcel->latitude }},{{ $parcel->longitude }}" target="_blank" rel="noopener noreferrer" class="text-brand hover:underline">{{ $parcel->latitude }}, {{ $parcel->longitude }}</a></dd>
                    @endif
                </dl>
            </section>
        @endforeach
    </div>

    @if ($parcel->proof)
        <section class="rounded-2xl border border-line bg-white p-6">
            <h2 class="mb-3 text-base font-semibold">Çatdırılma sübutu</h2>
            <p class="text-sm">Nağd {{ $parcel->proof->cash_pay }} ₼ · Post Terminal {{ $parcel->proof->pos_pay }} ₼ · Card to Card {{ $parcel->proof->card_pay }} ₼
                · {{ $parcel->delivered_at?->timezone('Asia/Baku')->format('d.m.Y H:i') }}</p>
            <p class="mt-1 text-xs text-muted">Foto və imza kuryer mobil tətbiqindən gələcək.</p>
        </section>
    @endif

    <section class="rounded-2xl border border-line bg-white p-6">
        <h2 class="mb-4 text-base font-semibold">Paket jurnalı</h2>
        <ol class="flex flex-col gap-4 border-l border-line pl-5">
            @foreach ($parcel->events as $event)
                <li wire:key="event-{{ $event->id }}" class="relative">
                    <span @class(['absolute top-1.5 -left-[26px] size-2.5 rounded-full', 'bg-brand' => ! $event->reverted, 'bg-line' => $event->reverted])></span>
                    <p @class(['text-sm font-semibold', 'line-through text-muted' => $event->reverted])>
                        {{ $actionLabels[$event->action] ?? $event->action }}
                        <span class="font-normal text-muted">→ {{ $event->status->label() }}</span>
                    </p>
                    <p class="text-xs text-muted">
                        {{ $event->created_at->timezone('Asia/Baku')->format('d.m.Y H:i') }}
                        @if ($event->actor) · {{ $event->actor->name }} ({{ $event->actor->email }})@endif
                        @if ($event->deliveryMan) · kuryer: {{ $event->deliveryMan->user->name }}@endif
                        @if ($event->hub) · {{ $event->hub->name }}@endif
                        @if (! empty($event->meta['date'])) · tarix: {{ $event->meta['date'] }}@endif
                        @if (! empty($event->meta['payment'])) · nağd {{ $event->meta['payment']['cash'] }}, POS {{ $event->meta['payment']['pos'] }}, kart {{ $event->meta['payment']['card'] }}@endif
                        @if ($event->latitude) · <a href="https://www.google.com/maps?q={{ $event->latitude }},{{ $event->longitude }}" target="_blank" rel="noopener noreferrer" class="text-brand hover:underline">xəritə</a>@endif
                    </p>
                    @if ($event->note)<p class="mt-0.5 text-sm">{{ $event->note }}</p>@endif
                </li>
            @endforeach
        </ol>
    </section>

    <livewire:parcels-action-panel />
</div>
