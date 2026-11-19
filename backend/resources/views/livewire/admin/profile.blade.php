<div class="grid items-start gap-5 lg:grid-cols-[1fr_400px]">
    <section class="rounded-2xl border border-line bg-white p-6">
        <div class="flex items-center gap-4">
            <span class="grid size-14 place-items-center rounded-full bg-brand-surface text-lg font-bold text-brand">{{ $user->initials() }}</span>
            <div>
                <h2 class="text-lg font-semibold">{{ $user->name }}</h2>
                <p class="text-sm text-muted">{{ $user->email }}</p>
            </div>
        </div>
        <dl class="mt-6 grid gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
            @foreach ([
                'Telefon' => $user->mobile,
                'Rol' => $user->role?->name,
                'Vəzifə' => $user->designation?->title,
                'Şöbə' => $user->department?->title,
                'Filiallar' => $user->hubs->pluck('name')->implode(', '),
                'Qoşulma tarixi' => $user->joining_date?->format('d.m.Y'),
                'Şəxsiyyət vəsiqəsi' => $user->nid_number,
                'Ünvan' => $user->address,
                'Son giriş' => $user->last_login_at?->timezone('Asia/Baku')->format('d.m.Y H:i'),
            ] as $label => $value)
                <div>
                    <dt class="text-xs font-semibold uppercase text-muted">{{ $label }}</dt>
                    <dd class="mt-0.5">{{ filled($value) ? $value : '—' }}</dd>
                </div>
            @endforeach
        </dl>
    </section>

    <form wire:submit="updatePassword" class="flex flex-col gap-4 rounded-2xl border border-line bg-white p-6">
        <h2 class="text-base font-semibold">Şifrəni dəyiş</h2>
        <x-admin.field label="Köhnə şifrə" for="p-old" error="old_password" required>
            <x-admin.input id="p-old" type="password" wire:model="old_password" autocomplete="current-password" :invalid="$errors->has('old_password')" />
        </x-admin.field>
        <x-admin.field label="Yeni şifrə" for="p-new" error="new_password" required hint="10–72 simvol.">
            <x-admin.input id="p-new" type="password" wire:model="new_password" autocomplete="new-password" :invalid="$errors->has('new_password')" />
        </x-admin.field>
        <x-admin.field label="Yeni şifrəni təsdiqlə" for="p-confirm" required>
            <x-admin.input id="p-confirm" type="password" wire:model="new_password_confirmation" autocomplete="new-password" />
        </x-admin.field>
        <div class="flex justify-end">
            <x-admin.button type="submit" wire:loading.attr="disabled" wire:target="updatePassword">Şifrəni dəyiş</x-admin.button>
        </div>
    </form>
</div>
