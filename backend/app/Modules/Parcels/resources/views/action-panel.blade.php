<div>
    <x-admin.modal wire:model="open" :title="$parcel ? 'Status: '.$parcel->tracking_id : 'Toplu əməliyyat'" max-width="max-w-2xl">
        <form wire:submit="submit" class="flex flex-col gap-4">
            @if ($parcel)
                <p class="text-sm text-muted">Hazırkı status: <b class="text-ink">{{ $parcel->status->label() }}</b> · Toplanmalı: <b class="text-ink">{{ $parcel->total }} ₼</b></p>
            @endif

            @error('action')<p role="alert" class="rounded-[10px] border border-brand/30 bg-brand-surface px-3 py-2.5 text-sm font-medium text-brand">{{ $message }}</p>@enderror

            @if (! $parcel)
                <div x-data="{ flash: null }" x-on:scan-result.window="flash = $event.detail.ok ? 'ok' : 'bad'; setTimeout(() => flash = null, 600)">
                    <x-admin.field label="Barkod / izləmə kodu (Enter)" for="bulk-scan">
                        <x-admin.input id="bulk-scan" wire:model="scan" wire:keydown.enter.prevent="addScan" autocomplete="off" autofocus
                                       class="font-mono uppercase" x-bind:class="flash === 'ok' ? '!border-emerald-500' : (flash === 'bad' ? '!border-brand' : '')" />
                    </x-admin.field>
                    <div class="mt-2 flex max-h-40 flex-wrap gap-1.5 overflow-y-auto">
                        @foreach ($trackingIds as $code)
                            <span wire:key="t-{{ $code }}" @class(['inline-flex items-center gap-1 rounded-full px-2.5 py-1 font-mono text-xs', 'bg-brand-surface text-brand' => isset($failed[$code]), 'bg-surface' => ! isset($failed[$code])]) title="{{ $failed[$code] ?? '' }}">
                                {{ $code }}
                                <button type="button" wire:click="removeTracking('{{ $code }}')" aria-label="Sil" class="text-muted hover:text-ink">×</button>
                            </span>
                        @endforeach
                    </div>
                    <p class="mt-1 text-xs text-muted">{{ count($trackingIds) }} paket seçilib.</p>
                    @if ($failed)
                        <ul class="mt-2 max-h-32 overflow-y-auto rounded-[10px] bg-brand-surface px-3 py-2 text-xs text-brand">
                            @foreach ($failed as $code => $reason)<li><b class="font-mono">{{ $code }}</b>: {{ $reason }}</li>@endforeach
                        </ul>
                    @endif
                </div>
            @endif

            <x-admin.field label="Əməliyyat" for="pa-action" required>
                <x-admin.select id="pa-action" wire:model.live="action" placeholder="{{ $actions ? 'Seçin' : 'Bu statusdan əməliyyat yoxdur' }}">
                    @foreach ($actions as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
                </x-admin.select>
            </x-admin.field>

            @if ($definition)
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach ($definition['fields'] as $field)
                        @php($required = in_array($field, $definition['required'], true))
                        @switch($field)
                            @case('delivery_man_id')
                                <x-admin.field label="Kuryer" for="pa-dm" error="delivery_man_id" :required="$required">
                                    <x-admin.select id="pa-dm" wire:model="input.delivery_man_id" placeholder="Seçin" :invalid="$errors->has('delivery_man_id')">
                                        @foreach ($couriers as $courier)<option value="{{ $courier->id }}">{{ $courier->user->name }}</option>@endforeach
                                    </x-admin.select>
                                </x-admin.field>
                                @break
                            @case('hub_id')
                                <x-admin.field label="Mərkəz" for="pa-hub" error="hub_id" :required="$required">
                                    <x-admin.select id="pa-hub" wire:model="input.hub_id" placeholder="Seçin" :invalid="$errors->has('hub_id')">
                                        @foreach ($hubs as $hub)<option value="{{ $hub->id }}">{{ $hub->name }}</option>@endforeach
                                    </x-admin.select>
                                </x-admin.field>
                                @break
                            @case('date')
                                <x-admin.field label="Tarix" for="pa-date" error="date" :required="$required">
                                    <x-admin.input id="pa-date" type="date" wire:model="input.date" :invalid="$errors->has('date')" />
                                </x-admin.field>
                                @break
                            @case('reject_reason_id')
                                <x-admin.field label="Qaytarma səbəbi" for="pa-reason" error="reject_reason_id">
                                    <x-admin.select id="pa-reason" wire:model="input.reject_reason_id" placeholder="Seçin">
                                        @foreach ($reasons as $reason)<option value="{{ $reason->id }}">{{ $reason->name }}</option>@endforeach
                                    </x-admin.select>
                                </x-admin.field>
                                @break
                            @case('collected')
                                <x-admin.field label="Toplanan nağd (AZN)" for="pa-collected" error="collected" required>
                                    <x-admin.input id="pa-collected" wire:model="input.collected" inputmode="decimal" :invalid="$errors->has('collected')" />
                                </x-admin.field>
                                @break
                            @case('payment')
                                <div class="grid grid-cols-3 gap-3 sm:col-span-2">
                                    @foreach (['cash_pay' => 'Nağd', 'pos_pay' => 'Post Terminal', 'card_pay' => 'Card to Card'] as $key => $label)
                                        <x-admin.field :label="$label" for="pa-{{ $key }}" :error="$key">
                                            <x-admin.input id="pa-{{ $key }}" wire:model="input.{{ $key }}" inputmode="decimal" placeholder="0.00" :invalid="$errors->has($key)" />
                                        </x-admin.field>
                                    @endforeach
                                    <p class="col-span-3 text-xs text-muted">Cəmi toplanmalı məbləğə{{ $parcel ? ' ('.$parcel->total.' ₼)' : '' }} bərabər olmalıdır.</p>
                                </div>
                                @break
                            @case('note')
                                <x-admin.field label="Qeyd" for="pa-note" error="note" class="sm:col-span-2">
                                    <x-admin.textarea id="pa-note" wire:model="input.note" rows="2" />
                                </x-admin.field>
                                @break
                        @endswitch
                    @endforeach
                </div>

                @if ($definition['sms'])
                    <fieldset class="flex flex-wrap gap-4 text-sm">
                        <legend class="mb-1 text-xs font-semibold uppercase text-muted">SMS göndər</legend>
                        @foreach (['courier' => 'Kuryerə', 'merchant' => 'Partnyora', 'customer' => 'Müştəriyə'] as $key => $label)
                            @if (in_array($key, $definition['sms'], true))
                                <label class="inline-flex items-center gap-1.5"><input type="checkbox" value="{{ $key }}" wire:model="input.sms" class="size-4 accent-brand"> {{ $label }}</label>
                            @endif
                        @endforeach
                    </fieldset>
                @endif
            @endif

            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    @if ($parcel && $parcel->events()->where('reverted', false)->whereNotIn('action', ['create', 'undo'])->exists())
                        <x-admin.button variant="danger" wire:click="undo" wire:confirm="Son əməliyyat geri alınsın?">Son əməliyyatı geri al</x-admin.button>
                    @endif
                </div>
                <div class="flex gap-2">
                    <x-admin.button variant="secondary" @click="show = false">İmtina</x-admin.button>
                    <x-admin.button type="submit" :disabled="! $definition" wire:loading.attr="disabled" wire:target="submit">Təsdiq et</x-admin.button>
                </div>
            </div>
        </form>
    </x-admin.modal>
</div>
