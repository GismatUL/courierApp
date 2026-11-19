<?php

use App\Http\Controllers\Admin\LocaleController;
use App\Http\Controllers\Admin\LogoutController;
use App\Http\Controllers\Admin\ShelfLabelController;
use App\Livewire\Admin\Auth\Login;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Management;
use App\Livewire\Admin\Profile;
use App\Livewire\Admin\Settings;
use App\Livewire\Admin\Users;
use Illuminate\Support\Facades\Route;

// The customer website is the separate Next.js app in frontend/; this app serves the admin panel and the API.
Route::redirect('/', '/admin');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::livewire('/login', Login::class)->name('login');
    });

    // Every page checks its spec permission key (config/permissions.php); actions re-check inside the components.
    Route::middleware(['auth', 'active'])->group(function () {
        Route::livewire('/', Dashboard::class)->name('dashboard');
        Route::livewire('/profile', Profile::class)->name('profile');
        Route::post('/logout', LogoutController::class)->name('logout');
        Route::get('/localization/{locale}', LocaleController::class)->name('locale');

        // İdarəetmə (spec §7)
        Route::livewire('/hubs', Management\Hubs::class)->name('hubs.index')->can('hub_read');
        Route::livewire('/zones', Management\Zones::class)->name('zones.index')->can('zone');
        Route::livewire('/zones/{zone}/tariffs', Management\ZoneTariffs::class)->name('zones.tariffs')->can('zone');
        Route::livewire('/regions', Management\Regions::class)->name('regions.index')->can('region');
        Route::livewire('/package-types', Management\PackageTypes::class)->name('package-types.index')->can('zone');
        Route::livewire('/shelves', Management\Shelves::class)->name('shelves.index')->can('shelf_read');
        Route::get('/shelves/{shelf}/label', ShelfLabelController::class)->name('shelves.label')->can('shelf_read');
        Route::livewire('/reject-reasons', Management\RejectReasons::class)->name('reject-reasons.index')->can('zone');

        // İstifadəçilər və Rollar (spec §9)
        Route::livewire('/roles', Users\Roles::class)->name('roles.index')->can('role_read');
        Route::livewire('/roles/create', Users\RoleEditor::class)->name('roles.create')->can('role_create');
        Route::livewire('/roles/{role}/edit', Users\RoleEditor::class)->name('roles.edit')->can('role_update');
        Route::livewire('/designations', Users\Designations::class)->name('designations.index')->can('designation_read');
        Route::livewire('/departments', Users\Departments::class)->name('departments.index')->can('department_read');
        Route::livewire('/users', Users\Users::class)->name('users.index')->can('user_read');
        Route::livewire('/users/{user}/permissions', Users\UserPermissions::class)->name('users.permissions')->can('permission_update');

        // Tənzimləmələr (spec §10.2–10.4)
        Route::livewire('/settings/general', Settings\General::class)->name('settings.general')->can('general_settings_read');
        Route::livewire('/settings/map', Settings\Map::class)->name('settings.map')->can('general_settings_read');
        Route::livewire('/currencies', Settings\Currencies::class)->name('currencies.index')->can('currency_read');
    });
});
