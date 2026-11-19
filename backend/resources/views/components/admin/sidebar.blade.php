{{-- Spec §2.1: collapsible groups; the parent layout owns the collapsed (icon-only) state. --}}
<nav class="flex flex-1 flex-col gap-1 overflow-y-auto px-3 py-4 text-sm" aria-label="Admin menyusu">
    @foreach ($menu as $entry)
        @isset($entry['items'])
            <div x-data="{ open: @js($entry['active']) }">
                <button type="button" @click="open = !open; if (collapsed) collapsed = false"
                        class="flex w-full items-center gap-3 rounded-lg px-3 py-2 font-medium text-ink hover:bg-surface {{ $entry['active'] ? 'text-brand' : '' }}"
                        :aria-expanded="open.toString()" :title="collapsed ? @js($entry['label']) : null">
                    <x-admin.icon :name="$entry['icon']" />
                    <span class="flex-1 truncate text-left" x-show="!collapsed">{{ $entry['label'] }}</span>
                    <x-admin.icon name="chevron-down" class="size-4 shrink-0 text-muted transition-transform" x-show="!collapsed" ::class="open && 'rotate-180'" />
                </button>
                <ul x-show="open && !collapsed" x-collapse class="mt-1 mb-2 ml-[22px] flex flex-col gap-0.5 border-l border-line pl-3">
                    @foreach ($entry['items'] as $item)
                        <li>
                            @if ($item['url'])
                                <a href="{{ $item['url'] }}" wire:navigate
                                   @class(['block rounded-md px-3 py-1.5 hover:bg-surface', 'bg-brand-surface font-semibold text-brand' => $item['active'], 'text-ink' => ! $item['active']])
                                   @if ($item['active']) aria-current="page" @endif>{{ $item['label'] }}</a>
                            @else
                                <span class="block cursor-not-allowed px-3 py-1.5 text-muted/60" title="Tezliklə">{{ $item['label'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <a href="{{ $entry['url'] }}" wire:navigate :title="collapsed ? @js($entry['label']) : null"
               @class(['flex items-center gap-3 rounded-lg px-3 py-2 font-medium hover:bg-surface', 'bg-brand-surface text-brand' => $entry['active'], 'text-ink' => ! $entry['active']])
               @if ($entry['active']) aria-current="page" @endif>
                <x-admin.icon :name="$entry['icon']" />
                <span class="truncate" x-show="!collapsed">{{ $entry['label'] }}</span>
            </a>
        @endisset
    @endforeach
</nav>
