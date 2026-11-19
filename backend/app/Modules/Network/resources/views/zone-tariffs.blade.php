<form wire:submit="save" class="flex flex-col gap-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <nav class="text-sm text-muted" aria-label="Breadcrumb">
            <a href="{{ route('admin.zones.index') }}" wire:navigate class="hover:text-ink">Zonalar</a>
            <span class="mx-1.5">/</span>
            <span class="font-medium text-ink">{{ $zone->name }}</span>
            @if ($zone->hub)
                <span class="ml-2 rounded-full bg-surface px-2 py-0.5 text-xs">{{ $zone->hub->name }}</span>
            @endif
        </nav>
        @can('zone_edit')<x-admin.button type="submit" wire:loading.attr="disabled" wire:target="save">Yadda saxla</x-admin.button>@endcan
    </div>

    <p class="rounded-[10px] bg-amber-surface px-3 py-2.5 text-sm text-muted">
        Qiyməti boş qalan sətir bu zonada təklif olunmur. Müddətlər sərbəst mətndir (məs. "30 dəqiqə", "1 gün").
    </p>

    @if ($errors->any())
        <p role="alert" class="rounded-[10px] border border-brand/30 bg-brand-surface px-3 py-2.5 text-sm font-medium text-brand">
            Yadda saxlanılmadı: {{ $errors->count() }} xanada xəta var (qırmızı ilə işarələnib).
        </p>
    @endif

    @if ($this->packageTypes->isEmpty())
        <p class="rounded-2xl border border-line bg-white px-6 py-10 text-center text-sm text-muted">
            Əvvəlcə <a href="{{ route('admin.package-types.index') }}" wire:navigate class="font-medium text-brand hover:underline">paket növləri</a> əlavə edin.
        </p>
    @endif

    @foreach ($this->packageTypes as $type)
        <section wire:key="type-{{ $type->id }}" class="overflow-hidden rounded-2xl border border-line bg-white">
            <header class="flex items-baseline gap-2 border-b border-line bg-surface/60 px-4 py-3">
                <h2 class="font-semibold">{{ $type->name }}</h2>
                <span class="text-xs text-muted">{{ $type->weightRange() }}</span>
                @unless ($type->status)
                    <x-admin.status-badge :active="false" />
                @endunless
            </header>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-left text-sm">
                    <thead class="text-xs font-semibold uppercase text-muted">
                        <tr>
                            <th class="w-44 px-4 py-2">Xidmət növü</th>
                            <th class="px-4 py-2">Götürülmə müddəti</th>
                            <th class="px-4 py-2">Çatdırılma müddəti</th>
                            <th class="w-40 px-4 py-2">Qiymət (AZN)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($tariffs as $tariff)
                            @php($base = "rows.{$type->id}.{$tariff->value}")
                            <tr wire:key="{{ $base }}">
                                <td class="px-4 py-2 font-medium">{{ $tariff->label() }}</td>
                                <td class="px-4 py-2">
                                    <x-admin.input wire:model="{{ $base }}.tracking_time" aria-label="{{ $type->name }} {{ $tariff->label() }} götürülmə müddəti" :invalid="$errors->has($base.'.tracking_time')" />
                                </td>
                                <td class="px-4 py-2">
                                    <x-admin.input wire:model="{{ $base }}.delivery_time" aria-label="{{ $type->name }} {{ $tariff->label() }} çatdırılma müddəti" :invalid="$errors->has($base.'.delivery_time')" />
                                </td>
                                <td class="px-4 py-2">
                                    <x-admin.input wire:model="{{ $base }}.price" inputmode="decimal" placeholder="—" aria-label="{{ $type->name }} {{ $tariff->label() }} qiymət" :invalid="$errors->has($base.'.price')" />
                                    @error($base.'.price')<p class="mt-1 text-xs text-brand">{{ $message }}</p>@enderror
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endforeach

    @if ($this->packageTypes->isNotEmpty())
        <div class="flex justify-end">
            @can('zone_edit')<x-admin.button type="submit" wire:loading.attr="disabled" wire:target="save">Yadda saxla</x-admin.button>@endcan
        </div>
    @endif
</form>
