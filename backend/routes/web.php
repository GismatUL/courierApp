<?php

use App\Http\Controllers\Admin\LocaleController;
use App\Http\Controllers\Admin\LogoutController;
use App\Http\Controllers\Admin\ShelfLabelController;
use App\Livewire\Admin\Auth\Login;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Management;
use App\Livewire\Admin\Settings;
use Illuminate\Support\Facades\Route;

// The customer website is the separate Next.js app in frontend/; this app serves the admin panel and the API.
Route::redirect('/', '/admin');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::livewire('/login', Login::class)->name('login');
    });

    Route::middleware(['auth', 'active'])->group(function () {
        Route::livewire('/', Dashboard::class)->name('dashboard');
        Route::post('/logout', LogoutController::class)->name('logout');
        Route::get('/localization/{locale}', LocaleController::class)->name('locale');

        // İdarəetmə (spec §7)
        Route::livewire('/hubs', Management\Hubs::class)->name('hubs.index');
        Route::livewire('/zones', Management\Zones::class)->name('zones.index');
        Route::livewire('/zones/{zone}/tariffs', Management\ZoneTariffs::class)->name('zones.tariffs');
        Route::livewire('/regions', Management\Regions::class)->name('regions.index');
        Route::livewire('/package-types', Management\PackageTypes::class)->name('package-types.index');
        Route::livewire('/shelves', Management\Shelves::class)->name('shelves.index');
        Route::get('/shelves/{shelf}/label', ShelfLabelController::class)->name('shelves.label');
        Route::livewire('/reject-reasons', Management\RejectReasons::class)->name('reject-reasons.index');

        // Tənzimləmələr (spec §10.2–10.4)
        Route::livewire('/settings/general', Settings\General::class)->name('settings.general');
        Route::livewire('/settings/map', Settings\Map::class)->name('settings.map');
        Route::livewire('/currencies', Settings\Currencies::class)->name('currencies.index');
    });
});
