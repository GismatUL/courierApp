<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title.' · ' : '' }}Kuryer.net Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface font-sans text-ink antialiased"
      x-data="{ collapsed: $persist(false).as('kuryer.sidebar.collapsed'), mobileNav: false }">
    {{-- Mobile off-canvas backdrop --}}
    <div x-cloak x-show="mobileNav" x-transition.opacity class="fixed inset-0 z-30 bg-ink/40 lg:hidden" @click="mobileNav = false"></div>

    <aside class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col border-r border-line bg-white transition-[translate,width] lg:translate-x-0"
           :class="{ 'translate-x-0': mobileNav, 'lg:w-20': collapsed, 'lg:w-72': !collapsed }">
        <div class="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-line px-5">
            <a href="{{ route('admin.dashboard') }}" wire:navigate class="block" aria-label="Kuryer.net — İdarə Paneli">
                <img src="{{ asset('logo.png') }}" alt="Kuryer.net" width="118" height="38" class="h-[38px] w-auto" x-show="!collapsed || mobileNav">
                <span x-cloak x-show="collapsed && !mobileNav" class="grid size-10 place-items-center rounded-lg bg-brand text-sm font-bold text-white">K</span>
            </a>
            <button type="button" class="rounded-md p-1.5 text-muted hover:bg-surface lg:hidden" @click="mobileNav = false" aria-label="Menyunu bağla">
                <x-admin.icon name="x" />
            </button>
        </div>
        <x-admin.sidebar />
        <p class="border-t border-line px-5 py-3 text-xs text-muted" x-show="!collapsed || mobileNav">© {{ date('Y') }} Kuryer.net</p>
    </aside>

    <div class="flex min-h-screen flex-col transition-[padding]" :class="collapsed ? 'lg:pl-20' : 'lg:pl-72'">
        <header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-line bg-white/95 px-4 backdrop-blur lg:px-8">
            <button type="button" class="rounded-md p-2 text-ink hover:bg-surface lg:hidden" @click="mobileNav = true" aria-label="Menyunu aç">
                <x-admin.icon name="menu" />
            </button>
            <button type="button" class="hidden rounded-md p-2 text-muted hover:bg-surface lg:block" @click="collapsed = !collapsed"
                    :aria-label="collapsed ? 'Menyunu genişləndir' : 'Menyunu yığ'">
                <x-admin.icon name="panel-left" />
            </button>
            <h1 class="min-w-0 flex-1 truncate text-lg font-semibold">{{ $title ?? 'İdarə Paneli' }}</h1>

            {{-- Language (spec §2.1) --}}
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button type="button" @click="open = !open" class="flex items-center gap-1.5 rounded-md px-2 py-2 text-sm font-medium text-ink hover:bg-surface" aria-label="Dil">
                    <x-admin.icon name="globe" class="size-4" /> <span class="uppercase">{{ app()->getLocale() }}</span>
                </button>
                <ul x-cloak x-show="open" x-transition class="absolute right-0 mt-1 w-40 rounded-lg border border-line bg-white py-1 text-sm shadow-lg">
                    @foreach (config('admin.locales') as $code => $label)
                        <li><a href="{{ route('admin.locale', $code) }}" @class(['block px-3 py-1.5 hover:bg-surface', 'font-semibold text-brand' => app()->getLocale() === $code])>{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Profile menu (spec §2.1, §2.4) --}}
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button type="button" @click="open = !open" class="flex items-center gap-2 rounded-full p-1 pr-2 hover:bg-surface" aria-label="Profil menyusu">
                    <span class="grid size-8 place-items-center rounded-full bg-brand-surface text-xs font-bold text-brand">{{ auth()->user()->initials() }}</span>
                    <span class="hidden max-w-40 truncate text-sm font-medium sm:block">{{ auth()->user()->name }}</span>
                    <x-admin.icon name="chevron-down" class="size-4 text-muted" />
                </button>
                <div x-cloak x-show="open" x-transition class="absolute right-0 mt-1 w-56 rounded-lg border border-line bg-white py-1 text-sm shadow-lg">
                    <p class="truncate border-b border-line px-3 py-2 text-xs text-muted">{{ auth()->user()->email }}</p>
                    <span class="flex cursor-not-allowed items-center gap-2 px-3 py-2 text-muted/60" title="Tezliklə"><x-admin.icon name="user" class="size-4" /> Profil</span>
                    <span class="flex cursor-not-allowed items-center gap-2 px-3 py-2 text-muted/60" title="Tezliklə"><x-admin.icon name="key-round" class="size-4" /> Şifrəni Dəyiş</span>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 px-3 py-2 text-left text-ink hover:bg-surface">
                            <x-admin.icon name="log-out" class="size-4" /> Çıxış
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="flex-1 px-4 py-6 lg:px-8">
            {{ $slot }}
        </main>
    </div>

    {{-- Toasts: Livewire components dispatch('toast', message: …, type: 'success'|'error'). --}}
    <div x-data="{ toasts: [] }" aria-live="polite"
         x-on:toast.window="const t = { id: Date.now() + Math.random(), ...$event.detail }; toasts.push(t); setTimeout(() => toasts = toasts.filter(x => x.id !== t.id), 4000)"
         class="pointer-events-none fixed right-4 bottom-4 z-[60] flex w-full max-w-sm flex-col gap-2">
        <template x-for="t in toasts" :key="t.id">
            <div x-transition class="pointer-events-auto flex items-start gap-2.5 rounded-xl border bg-white px-4 py-3 text-sm font-medium shadow-lg"
                 :class="t.type === 'error' ? 'border-brand/30 text-brand' : 'border-emerald-200 text-emerald-800'" role="status">
                <span x-text="t.message"></span>
            </div>
        </template>
    </div>
</body>
</html>
