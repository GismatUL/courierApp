@props(['selected' => [], 'disabled' => false])

{{-- Spec §9.1: "Modullar | İcazələr" — one row per module; checkboxes bind to the Livewire "permissions" array. --}}
<div class="overflow-hidden rounded-2xl border border-line bg-white">
    <div class="flex items-center justify-between gap-3 border-b border-line bg-surface/60 px-4 py-3 text-xs font-semibold uppercase text-muted">
        <span>Modullar</span>
        <span>{{ count($selected) }} / {{ count(\App\Shared\Support\Permissions::all()) }} icazə</span>
    </div>
    <div class="divide-y divide-line">
        @foreach (\App\Shared\Support\Permissions::modules() as $module => $group)
            @php($keys = array_keys($group['keys']))
            @php($on = count(array_intersect($keys, $selected)))
            <div wire:key="module-{{ $module }}" class="grid gap-3 px-4 py-3 md:grid-cols-[220px_1fr]">
                <div class="flex items-start justify-between gap-2 md:flex-col md:justify-start">
                    <span class="text-sm font-semibold">{{ $group['label'] }}</span>
                    @unless ($disabled)
                        <button type="button" wire:click="toggleModule('{{ $module }}')" class="text-xs font-medium text-brand hover:underline">
                            {{ $on === count($keys) ? 'Hamısını sil' : 'Hamısını seç' }} ({{ $on }}/{{ count($keys) }})
                        </button>
                    @endunless
                </div>
                <div class="flex flex-wrap gap-x-4 gap-y-2">
                    @foreach ($group['keys'] as $key => $label)
                        <label class="flex items-center gap-1.5 text-sm" title="{{ $key }}">
                            <input type="checkbox" value="{{ $key }}" wire:model.live="permissions" class="size-4 accent-brand" @disabled($disabled)>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
