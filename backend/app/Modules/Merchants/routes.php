<?php

use App\Modules\Merchants\Http\MerchantFileController;
use App\Modules\Merchants\Livewire;
use Illuminate\Support\Facades\Route;

// Partnyor idarəetməsi (spec §6). Loaded by MerchantsServiceProvider under /admin, name "admin.", "web" middleware.

Route::middleware(['auth', 'active'])->group(function () {
    Route::livewire('/merchants', Livewire\Merchants::class)->name('merchants.index')->can('merchant_read');
    Route::livewire('/merchants/create', Livewire\MerchantEditor::class)->name('merchants.create')->can('merchant_create');
    Route::livewire('/merchants/{merchant}', Livewire\MerchantShow::class)->name('merchants.show')->can('merchant_view');
    Route::livewire('/merchants/{merchant}/edit', Livewire\MerchantEditor::class)->name('merchants.edit')->can('merchant_update');
    Route::get('/merchants/{merchant}/files/{column}', MerchantFileController::class)->name('merchants.file')->can('merchant_view')
        ->whereIn('column', ['nid_file', 'trade_license_file', 'image_file']);
    Route::livewire('/merchant-types', Livewire\MerchantTypes::class)->name('merchant-types.index')->can('merchant_read');
});
