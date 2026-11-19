@props(['invalid' => false])

<input {{ $attributes->merge(['type' => 'text'])->class([
    'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-ink outline-none placeholder:text-muted/70 focus:border-brand disabled:bg-surface',
    'border-brand' => $invalid,
    'border-line' => ! $invalid,
]) }}>
