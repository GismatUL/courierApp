<?php

use App\Modules\Network\Http\ShelfLabelController;
use App\Modules\Network\Livewire;
use Illuminate\Support\Facades\Route;

// İdarəetmə (spec §7). Loaded by NetworkServiceProvider under /admin, name "admin.", "web" middleware.

Route::middleware(['auth', 'active'])->group(function () {
    Route::livewire('/hubs', Livewire\Hubs::class)->name('hubs.index')->can('hub_read');
    Route::livewire('/zones', Livewire\Zones::class)->name('zones.index')->can('zone');
    Route::livewire('/zones/{zone}/tariffs', Livewire\ZoneTariffs::class)->name('zones.tariffs')->can('zone');
    Route::livewire('/regions', Livewire\Regions::class)->name('regions.index')->can('region');
    Route::livewire('/package-types', Livewire\PackageTypes::class)->name('package-types.index')->can('zone');
    Route::livewire('/shelves', Livewire\Shelves::class)->name('shelves.index')->can('shelf_read');
    Route::get('/shelves/{shelf}/label', ShelfLabelController::class)->name('shelves.label')->can('shelf_read');
    Route::livewire('/reject-reasons', Livewire\RejectReasons::class)->name('reject-reasons.index')->can('zone');
});
