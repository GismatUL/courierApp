@props(['title', 'maxWidth' => 'max-w-xl'])

{{-- Dialog bound to a boolean Livewire property: <x-admin.modal wire:model="showForm" title="…"> --}}
<div x-data="{ show: @entangle($attributes->wire('model')) }" x-show="show" x-cloak
     x-on:keydown.escape.window="show = false"
     class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-ink/40 px-4 py-10" role="dialog" aria-modal="true">
    <div x-show="show" x-transition @click.outside="show = false" class="w-full {{ $maxWidth }} rounded-2xl bg-white shadow-xl">
        <div class="flex items-center justify-between gap-4 border-b border-line px-6 py-4">
            <h2 class="text-base font-semibold">{{ $title }}</h2>
            <button type="button" class="rounded-md p-1 text-muted hover:bg-surface" @click="show = false" aria-label="Bağla">
                <x-admin.icon name="x" class="size-5" />
            </button>
        </div>
        <div class="px-6 py-5">{{ $slot }}</div>
    </div>
</div>
