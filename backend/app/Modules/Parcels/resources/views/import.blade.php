<div class="flex max-w-3xl flex-col gap-5">
    <nav class="text-sm text-muted" aria-label="Breadcrumb">
        <a href="{{ route('admin.parcels.index') }}" wire:navigate class="hover:text-ink">Paketlər</a><span class="mx-1.5">/</span><span class="font-medium text-ink">Import Excel</span>
    </nav>

    <form wire:submit="import" class="flex flex-col gap-4 rounded-2xl border border-line bg-white p-6">
        <p class="text-sm text-muted">
            Sütunlar nümunə şablondakı kimi olmalıdır; zona və paket növü adla, tarif adla (Təcili, Gün ərzində, Regular, Ekspress) yazılır.
            Hər sətir əl ilə yaradılan paket kimi yoxlanılır; bir sətir belə xətalıdırsa, heç nə import edilmir.
            <a href="{{ route('admin.parcels.import-template') }}" class="font-semibold text-brand hover:underline">Nümunəni yüklə</a>
        </p>
        <x-admin.field label="Partnyor" for="i-merchant" error="merchantId" required>
            <x-admin.select id="i-merchant" wire:model="merchantId" placeholder="Seçin" :invalid="$errors->has('merchantId')">
                @foreach ($merchants as $m)<option value="{{ $m->id }}">{{ $m->business_name }}</option>@endforeach
            </x-admin.select>
        </x-admin.field>
        <x-admin.field label="Fayl (.xlsx)" for="i-file" error="file" required>
            <input id="i-file" type="file" wire:model="file" accept=".xlsx" class="text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-brand-surface file:px-3 file:py-2 file:text-sm file:font-semibold file:text-brand">
        </x-admin.field>
        <div class="flex justify-end"><x-admin.button type="submit" wire:loading.attr="disabled" wire:target="import,file">Import et</x-admin.button></div>
    </form>

    @if ($imported !== null)
        <p role="status" class="rounded-[10px] border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-sm font-medium text-emerald-800">{{ $imported }} paket import edildi.</p>
    @endif

    @if ($rowErrors)
        <section role="alert" class="rounded-2xl border border-brand/30 bg-white p-6">
            <h2 class="mb-2 text-base font-semibold text-brand">Import edilmədi: {{ count($rowErrors) }} sətirdə xəta var</h2>
            <ul class="flex flex-col gap-1 text-sm">
                @foreach ($rowErrors as $number => $message)<li><b>Sətir {{ $number }}:</b> {{ $message }}</li>@endforeach
            </ul>
        </section>
    @endif
</div>
