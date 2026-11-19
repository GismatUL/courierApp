<form wire:submit="save" class="flex max-w-2xl flex-col gap-5 rounded-2xl border border-line bg-white p-6">
    <x-admin.field label="Google Maps API açarı" for="map-key" error="mapKey">
        <x-admin.input id="map-key" wire:model="mapKey" class="font-mono" autocomplete="off" spellcheck="false" placeholder="AIza…" :invalid="$errors->has('mapKey')" />
    </x-admin.field>

    <div class="rounded-[10px] bg-amber-surface px-3 py-2.5 text-sm text-muted">
        Açar admin xəritələrində və ictimai ünvan təsdiqi səhifəsində brauzerdə istifadə olunur, yəni açıq görünür.
        Google Cloud-da onu <b class="text-ink">HTTP referrer</b> (yalnız sizin domenlər) və <b class="text-ink">Maps JavaScript / Places API</b> ilə məhdudlaşdırın.
    </div>

    <div class="flex justify-end">
        @can('general_settings_update')<x-admin.button type="submit" wire:loading.attr="disabled" wire:target="save">Yadda saxla</x-admin.button>@endcan
    </div>
</form>
