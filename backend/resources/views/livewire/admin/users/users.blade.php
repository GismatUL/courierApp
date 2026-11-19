<div class="flex flex-col gap-5">
    <x-admin.page-header description="Panel istifadəçiləri. Rol seçildikdə icazələr rol şablonundan köçürülür; sonra istifadəçi üzrə dəyişdirilə bilər.">
        @if ($this->can('create'))<x-admin.button wire:click="create"><x-admin.icon name="plus" class="size-4" /> Əlavə et</x-admin.button>@endif
    </x-admin.page-header>

    <x-admin.filters>
        <x-admin.field label="Ad" for="f-name" class="w-full sm:w-48">
            <x-admin.input id="f-name" wire:model.live.debounce.300ms="name" placeholder="Axtar…" />
        </x-admin.field>
        <x-admin.field label="Email" for="f-email" class="w-full sm:w-48">
            <x-admin.input id="f-email" wire:model.live.debounce.300ms="email" placeholder="Axtar…" />
        </x-admin.field>
        <x-admin.field label="Telefon" for="f-phone" class="w-full sm:w-48">
            <x-admin.input id="f-phone" wire:model.live.debounce.300ms="phone" placeholder="Axtar…" />
        </x-admin.field>
    </x-admin.filters>

    <x-admin.table :empty="$users->isEmpty()">
        <x-slot:head>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">Təfərrüatlar</th>
            <th class="px-4 py-3">Rol</th>
            <th class="px-4 py-3">İcazələr</th>
            <th class="px-4 py-3">Maaş</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Fəaliyyətlər</th>
        </x-slot:head>

        @foreach ($users as $user)
            @php($locked = $user->isSuperAdmin() && ! auth()->user()->isSuperAdmin())
            <tr wire:key="user-{{ $user->id }}" class="hover:bg-surface/50">
                <td class="px-4 py-3 text-muted">{{ $users->firstItem() + $loop->index }}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <span class="grid size-9 shrink-0 place-items-center rounded-full bg-brand-surface text-xs font-bold text-brand">{{ $user->initials() }}</span>
                        <div class="min-w-0">
                            <p class="truncate font-semibold">{{ $user->name }} @if ($user->is(auth()->user()))<span class="text-xs font-normal text-muted">(siz)</span>@endif</p>
                            <p class="truncate text-xs text-muted">{{ $user->email }}@if ($user->mobile) · {{ $user->mobile }}@endif</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3">{{ $user->role?->name ?? '—' }}</td>
                <td class="px-4 py-3">{{ $user->isSuperAdmin() ? 'hamısı' : count($user->permissions ?? []) }}</td>
                <td class="px-4 py-3 whitespace-nowrap">{{ (float) $user->salary ? number_format($user->salary, 2, '.', ' ').' ₼' : '—' }}</td>
                <td class="px-4 py-3"><x-admin.status-badge :active="$user->status" /></td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-1">
                        @if ($this->can('permissions') && ! $user->isSuperAdmin() && ! $user->is(auth()->user()))
                            <x-admin.button variant="ghost" size="sm" :href="route('admin.users.permissions', $user)" wire:navigate aria-label="İcazələr"><x-admin.icon name="key-round" class="size-4" /></x-admin.button>
                        @endif
                        @if ($this->can('update') && ! $locked)<x-admin.button variant="ghost" size="sm" wire:click="edit({{ $user->id }})" aria-label="Redaktə et"><x-admin.icon name="pencil" class="size-4" /></x-admin.button>@endif
                        @if ($this->can('delete') && ! $locked && ! $user->is(auth()->user()))<x-admin.button variant="ghost" size="sm" wire:click="confirmDelete({{ $user->id }})" aria-label="Sil"><x-admin.icon name="trash-2" class="size-4" /></x-admin.button>@endif
                    </div>
                </td>
            </tr>
        @endforeach

        @if ($users->hasPages())
            <x-slot:footer>{{ $users->links() }}</x-slot:footer>
        @endif
    </x-admin.table>

    <x-admin.modal wire:model="showForm" :title="$form->id ? 'İstifadəçini redaktə et' : 'Yeni istifadəçi'" max-width="max-w-3xl">
        <form wire:submit="save" class="grid gap-4 sm:grid-cols-2" autocomplete="off">
            <x-admin.field label="Ad" for="u-name" error="form.name" required>
                <x-admin.input id="u-name" wire:model="form.name" :invalid="$errors->has('form.name')" />
            </x-admin.field>
            <x-admin.field label="Email (giriş)" for="u-email" error="form.email" required>
                <x-admin.input id="u-email" type="email" wire:model="form.email" autocomplete="off" :invalid="$errors->has('form.email')" />
            </x-admin.field>
            <x-admin.field label="Şifrə" for="u-password" error="form.password" :required="! $form->id" :hint="$form->id ? 'Boş qalsa, şifrə dəyişmir.' : '10–72 simvol.'">
                <x-admin.input id="u-password" type="password" wire:model="form.password" autocomplete="new-password" :invalid="$errors->has('form.password')" />
            </x-admin.field>
            <x-admin.field label="Telefon" for="u-mobile" error="form.mobile">
                <x-admin.input id="u-mobile" wire:model="form.mobile" placeholder="+994 50 000 00 00" :invalid="$errors->has('form.mobile')" />
            </x-admin.field>
            <x-admin.field label="Rol" for="u-role" error="form.role_id" required :hint="$form->id ? 'Rol dəyişdikdə icazələr yeni rolun şablonundan köçürülür.' : null">
                <x-admin.select id="u-role" wire:model="form.role_id" placeholder="Seçin" :invalid="$errors->has('form.role_id')">
                    @foreach ($roles as $role)
                        @if (! $role->isSuperAdmin() || auth()->user()->isSuperAdmin())
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endif
                    @endforeach
                </x-admin.select>
            </x-admin.field>
            <x-admin.field label="Vəzifə" for="u-designation" error="form.designation_id">
                <x-admin.select id="u-designation" wire:model="form.designation_id" placeholder="Seçin">
                    @foreach ($designations as $designation)
                        <option value="{{ $designation->id }}">{{ $designation->title }}</option>
                    @endforeach
                </x-admin.select>
            </x-admin.field>
            <x-admin.field label="Şöbə" for="u-department" error="form.department_id">
                <x-admin.select id="u-department" wire:model="form.department_id" placeholder="Seçin">
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->title }}</option>
                    @endforeach
                </x-admin.select>
            </x-admin.field>
            <x-admin.field label="Maaş (AZN)" for="u-salary" error="form.salary">
                <x-admin.input id="u-salary" wire:model="form.salary" inputmode="decimal" :invalid="$errors->has('form.salary')" />
            </x-admin.field>
            <x-admin.field label="Qoşulma tarixi" for="u-joined" error="form.joining_date">
                <x-admin.input id="u-joined" type="date" wire:model="form.joining_date" :invalid="$errors->has('form.joining_date')" />
            </x-admin.field>
            <x-admin.field label="Şəxsiyyət vəsiqəsi" for="u-nid" error="form.nid_number">
                <x-admin.input id="u-nid" wire:model="form.nid_number" :invalid="$errors->has('form.nid_number')" />
            </x-admin.field>
            <x-admin.field label="Ünvan" for="u-address" error="form.address" class="sm:col-span-2">
                <x-admin.input id="u-address" wire:model="form.address" />
            </x-admin.field>
            <fieldset class="flex flex-col gap-1.5 sm:col-span-2">
                <legend class="mb-1.5 text-xs font-semibold uppercase text-muted">Görə bildiyi filiallar</legend>
                @forelse ($hubs as $hub)
                    <label class="mr-4 inline-flex items-center gap-1.5 text-sm">
                        <input type="checkbox" value="{{ $hub->id }}" wire:model="form.hub_ids" class="size-4 accent-brand"> {{ $hub->name }}
                    </label>
                @empty
                    <p class="text-sm text-muted">Hələ filial yoxdur.</p>
                @endforelse
            </fieldset>
            <label class="flex items-center gap-2 text-sm sm:col-span-2">
                <input type="checkbox" wire:model="form.status" class="size-4 accent-brand"> Aktiv
            </label>
            <div class="flex justify-end gap-2 sm:col-span-2">
                <x-admin.button variant="secondary" @click="show = false">İmtina</x-admin.button>
                <x-admin.button type="submit" wire:loading.attr="disabled" wire:target="save">Yadda saxla</x-admin.button>
            </div>
        </form>
    </x-admin.modal>

    <x-admin.confirm-delete />
</div>
