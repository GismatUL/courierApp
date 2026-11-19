<div class="flex flex-col gap-6">
    <section class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-6" aria-label="Göstəricilər">
        @foreach ($cards as $card)
            <div class="flex flex-col gap-2 rounded-2xl border border-line bg-white p-5">
                <span class="text-xs font-semibold uppercase text-muted">{{ $card['label'] }}</span>
                <span class="text-2xl font-bold">{{ $card['value'] ?? '—' }}</span>
            </div>
        @endforeach
    </section>

    <section class="rounded-2xl border border-line bg-white p-6">
        <h2 class="text-base font-semibold">Xoş gəldiniz, {{ auth()->user()->name }}</h2>
        <p class="mt-1 text-sm text-muted">
            Modullar texniki tapşırıqdakı ardıcıllıqla əlavə olunur. Menyuda boz görünən bölmələr hələ hazır deyil.
        </p>
    </section>
</div>
