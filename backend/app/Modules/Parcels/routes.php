<?php

use App\Modules\Parcels\Http\ParcelFileController;
use App\Modules\Parcels\Livewire;
use Illuminate\Support\Facades\Route;

// Paketlər (spec §4). Loaded by ParcelsServiceProvider under /admin, name "admin.", "web" middleware.

Route::middleware(['auth', 'active'])->group(function () {
    Route::livewire('/parcels', Livewire\Parcels::class)->name('parcels.index')->can('parcel_read');
    Route::livewire('/parcels/create', Livewire\ParcelEditor::class)->name('parcels.create')->can('parcel_create');
    Route::livewire('/parcels/import', Livewire\ImportParcels::class)->name('parcels.import')->can('parcel_create');
    Route::get('/parcels/import/template', [ParcelFileController::class, 'importTemplate'])->name('parcels.import-template')->can('parcel_create');
    Route::get('/parcels/export', [ParcelFileController::class, 'export'])->name('parcels.export')->can('parcel_read');
    Route::get('/parcels/labels', [ParcelFileController::class, 'labels'])->name('parcels.labels')->can('parcel_read');
    Route::livewire('/parcels/{parcel}', Livewire\ParcelShow::class)->name('parcels.show')->can('parcel_read');
    Route::livewire('/parcels/{parcel}/edit', Livewire\ParcelEditor::class)->name('parcels.edit')->can('parcel_update');
});
