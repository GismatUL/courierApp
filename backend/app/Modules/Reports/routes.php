<?php

use App\Modules\Reports\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

// Loaded by ReportsServiceProvider under /admin, name "admin.", "web" middleware.

Route::middleware(['auth', 'active'])->group(function () {
    Route::livewire('/', Dashboard::class)->name('dashboard');
});
