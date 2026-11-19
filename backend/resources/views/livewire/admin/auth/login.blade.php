<div class="flex flex-col gap-6 rounded-3xl bg-white p-8 shadow-[0_12px_40px_rgba(89,26,31,0.06)]">
    <img src="{{ asset('logo.png') }}" alt="Kuryer.net" width="148" height="48" class="h-12 w-auto self-start">

    <div class="flex flex-col gap-1.5">
        <h2 class="text-2xl font-bold">İdarə panelinə giriş</h2>
        <p class="text-sm text-muted">Email və şifrənizi daxil edin.</p>
    </div>

    <form wire:submit="login" class="flex flex-col gap-4" novalidate>
        <label class="flex flex-col gap-1.5">
            <span class="text-xs font-semibold uppercase text-muted">Email</span>
            <input type="email" wire:model="email" autocomplete="username" autofocus required
                   @class(['rounded-xl border bg-white px-4 py-3 text-[15px] outline-none focus:border-brand', 'border-brand' => $errors->has('email'), 'border-line' => ! $errors->has('email')])
                   aria-describedby="email-error" @error('email') aria-invalid="true" @enderror>
        </label>

        <label class="flex flex-col gap-1.5">
            <span class="text-xs font-semibold uppercase text-muted">Şifrə</span>
            <input type="password" wire:model="password" autocomplete="current-password" required
                   @class(['rounded-xl border bg-white px-4 py-3 text-[15px] outline-none focus:border-brand', 'border-brand' => $errors->has('password'), 'border-line' => ! $errors->has('password')])>
        </label>

        @if ($errors->any())
            <p id="email-error" role="alert" class="rounded-[10px] bg-amber-surface px-3 py-2.5 text-sm font-medium text-brand">{{ $errors->first() }}</p>
        @endif

        <label class="flex items-center gap-2 text-sm text-ink">
            <input type="checkbox" wire:model="remember" class="size-4 rounded border-line accent-brand"> Məni xatırla
        </label>

        <button type="submit" wire:loading.attr="disabled"
                class="rounded-xl bg-brand px-5 py-3.5 text-[15px] font-semibold text-white hover:bg-brand-hover active:bg-brand-active disabled:opacity-60">
            <span wire:loading.remove wire:target="login">Daxil ol</span>
            <span wire:loading wire:target="login">Yoxlanılır…</span>
        </button>
    </form>
</div>
