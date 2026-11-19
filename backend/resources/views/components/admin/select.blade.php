@props(['invalid' => false, 'placeholder' => null])

<select {{ $attributes->class([
    'w-full rounded-xl border bg-white px-3 py-2.5 text-sm text-ink outline-none focus:border-brand',
    'border-brand' => $invalid,
    'border-line' => ! $invalid,
]) }}>
    @if ($placeholder !== null)
        <option value="">{{ $placeholder }}</option>
    @endif
    {{ $slot }}
</select>
