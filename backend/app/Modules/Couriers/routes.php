<?php

use App\Modules\Couriers\Http\CourierFileController;
use App\Modules\Couriers\Livewire;
use Illuminate\Support\Facades\Route;

// Kuryerlər (spec §7.2). Loaded by CouriersServiceProvider under /admin, name "admin.", "web" middleware.

Route::middleware(['auth', 'active'])->group(function () {
    Route::livewire('/couriers', Livewire\Couriers::class)->name('couriers.index')->can('delivery_man_read');
    Route::livewire('/couriers/create', Livewire\CourierEditor::class)->name('couriers.create')->can('delivery_man_create');
    Route::livewire('/couriers/{courier}/edit', Livewire\CourierEditor::class)->name('couriers.edit')->can('delivery_man_update');
    Route::get('/couriers/{courier}/files/{column}', CourierFileController::class)->name('couriers.file')->can('delivery_man_read')
        ->whereIn('column', ['image_file', 'driving_license_file']);
});
