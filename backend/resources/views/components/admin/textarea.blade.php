@props(['invalid' => false])

<textarea {{ $attributes->merge(['rows' => 3])->class([
    'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-ink outline-none focus:border-brand',
    'border-brand' => $invalid,
    'border-line' => ! $invalid,
]) }}></textarea>
