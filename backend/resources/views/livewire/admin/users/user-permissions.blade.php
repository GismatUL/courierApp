<form wire:submit="save" class="flex flex-col gap-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <nav class="text-sm text-muted" aria-label="Breadcrumb">
            <a href="{{ route('admin.users.index') }}" wire:navigate class="hover:text-ink">İstifadəçilər</a>
            <span class="mx-1.5">/</span>
            <span class="font-medium text-ink">{{ $user->name }}</span>
            <span class="ml-2 rounded-full bg-surface px-2 py-0.5 text-xs">{{ $user->role?->name ?? 'Rolsuz' }}</span>
        </nav>
        <div class="flex gap-2">
            @if ($user->role)
                <x-admin.button variant="secondary" wire:click="resetToRole">Rol şablonuna qaytar</x-admin.button>
            @endif
            <x-admin.button type="submit" wire:loading.attr="disabled" wire:target="save">Yadda saxla</x-admin.button>
        </div>
    </div>

    @error('permissions')
        <p role="alert" class="rounded-[10px] border border-brand/30 bg-brand-surface px-3 py-2.5 text-sm font-medium text-brand">{{ $message }}</p>
    @enderror

    <x-admin.permission-matrix :selected="$permissions" />
</form>
