@props(['description' => null])

{{-- Page intro + primary actions (spec §2.1: every list has a header, filters and actions). --}}
<div class="flex flex-wrap items-end justify-between gap-4">
    <div class="min-w-0">
        @if ($description)
            <p class="text-sm text-muted">{{ $description }}</p>
        @endif
    </div>
    <div class="flex flex-wrap items-center gap-2">{{ $slot }}</div>
</div>
