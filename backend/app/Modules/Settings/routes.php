<?php

use App\Modules\Settings\Livewire;
use Illuminate\Support\Facades\Route;

// Tənzimləmələr (spec §10.2–10.4). Loaded by SettingsServiceProvider under /admin, name "admin.", "web" middleware.

Route::middleware(['auth', 'active'])->group(function () {
    Route::livewire('/settings/general', Livewire\General::class)->name('settings.general')->can('general_settings_read');
    Route::livewire('/settings/map', Livewire\Map::class)->name('settings.map')->can('general_settings_read');
    Route::livewire('/currencies', Livewire\Currencies::class)->name('currencies.index')->can('currency_read');
});
