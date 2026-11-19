@props(['variant' => 'primary', 'size' => 'md', 'href' => null])

@php
    $classes = \Illuminate\Support\Arr::toCssClasses([
        'inline-flex items-center justify-center gap-2 rounded-xl font-semibold transition-colors disabled:cursor-not-allowed disabled:opacity-60 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand',
        'px-4 py-2.5 text-sm' => $size === 'md',
        'px-2.5 py-1.5 text-xs' => $size === 'sm',
        'bg-brand text-white hover:bg-brand-hover active:bg-brand-active' => $variant === 'primary',
        'border border-line bg-white text-ink hover:bg-surface' => $variant === 'secondary',
        'bg-brand-surface text-brand hover:bg-amber-surface' => $variant === 'danger',
        'text-muted hover:bg-surface hover:text-ink' => $variant === 'ghost',
    ]);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => $classes]) }}>{{ $slot }}</button>
@endif
