<?php

use App\Shared\Http\LocaleController;
use Illuminate\Support\Facades\Route;

// The customer website is the separate Next.js app in frontend/; this app serves the admin panel and the API.
// Admin pages are registered by each module's routes.php (app/Modules/*), see ModuleServiceProvider.
Route::redirect('/', '/admin');

Route::middleware(['auth', 'active'])->get('/admin/localization/{locale}', LocaleController::class)->name('admin.locale');
