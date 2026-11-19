@props(['label', 'for' => null, 'error' => null, 'required' => false, 'hint' => null])

{{-- Label + control + validation message. `error` is the validation key (e.g. form.name). --}}
<div {{ $attributes->merge(['class' => 'flex flex-col gap-1.5']) }}>
    <label @if ($for) for="{{ $for }}" @endif class="text-xs font-semibold uppercase text-muted">
        {{ $label }}@if ($required)<span class="text-brand"> *</span>@endif
    </label>
    {{ $slot }}
    @if ($hint)
        <p class="text-xs text-muted">{{ $hint }}</p>
    @endif
    @if ($error)
        @error($error)
            <p class="text-xs font-medium text-brand" role="alert">{{ $message }}</p>
        @enderror
    @endif
</div>
