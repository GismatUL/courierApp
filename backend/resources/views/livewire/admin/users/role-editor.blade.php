<form wire:submit="save" class="flex flex-col gap-5">
    <nav class="text-sm text-muted" aria-label="Breadcrumb">
        <a href="{{ route('admin.roles.index') }}" wire:navigate class="hover:text-ink">Rollar</a>
        <span class="mx-1.5">/</span>
        <span class="font-medium text-ink">{{ $role?->name ?? 'Yeni rol' }}</span>
    </nav>

    <section class="grid gap-4 rounded-2xl border border-line bg-white p-6 sm:grid-cols-[1fr_auto] sm:items-end">
        <x-admin.field label="Rol adı" for="role-name" error="name" required>
            <x-admin.input id="role-name" wire:model="name" :invalid="$errors->has('name')" />
        </x-admin.field>
        @unless ($role?->isSuperAdmin())
            <label class="flex items-center gap-2 pb-2.5 text-sm">
                <input type="checkbox" wire:model="status" class="size-4 accent-brand"> Aktiv
            </label>
        @endunless
    </section>

    @error('permissions')
        <p role="alert" class="rounded-[10px] border border-brand/30 bg-brand-surface px-3 py-2.5 text-sm font-medium text-brand">{{ $message }}</p>
    @enderror

    @if ($role?->isSuperAdmin())
        <p class="rounded-[10px] bg-amber-surface px-3 py-2.5 text-sm text-muted">Super Admin bütün icazələrə avtomatik malikdir; icazə matrisi dəyişdirilmir.</p>
    @else
        <x-admin.permission-matrix :selected="$permissions" />
    @endif

    <div class="flex justify-end gap-2">
        <x-admin.button variant="secondary" :href="route('admin.roles.index')" wire:navigate>İmtina</x-admin.button>
        <x-admin.button type="submit" wire:loading.attr="disabled" wire:target="save">Yadda saxla</x-admin.button>
    </div>
</form>
