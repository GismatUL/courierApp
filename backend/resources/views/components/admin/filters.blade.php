{{-- Filter bar (spec §2.1 "Filtr" / "Təmizlə"); inputs filter live, "Təmizlə" calls resetFilters(). --}}
<div {{ $attributes->merge(['class' => 'flex flex-wrap items-end gap-3 rounded-2xl border border-line bg-white p-4']) }}>
    {{ $slot }}
    <x-admin.button variant="ghost" wire:click="resetFilters">Təmizlə</x-admin.button>
</div>
