@props(['empty' => false])

<div class="overflow-hidden rounded-2xl border border-line bg-white">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[640px] text-left text-sm">
            @isset($head)
                <thead class="border-b border-line bg-surface/60 text-xs font-semibold uppercase text-muted">
                    <tr>{{ $head }}</tr>
                </thead>
            @endisset
            <tbody class="divide-y divide-line">{{ $slot }}</tbody>
        </table>
    </div>
    @if ($empty)
        <p class="px-6 py-10 text-center text-sm text-muted">Məlumat tapılmadı.</p>
    @endif
    @isset($footer)
        <div class="border-t border-line px-4 py-3">{{ $footer }}</div>
    @endisset
</div>
