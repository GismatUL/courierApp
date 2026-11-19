<?php

use App\Modules\Access\Http\LogoutController;
use App\Modules\Access\Livewire;
use Illuminate\Support\Facades\Route;

// Loaded by AccessServiceProvider under /admin, name "admin.", "web" middleware.

Route::middleware('guest')->group(function () {
    Route::livewire('/login', Livewire\Login::class)->name('login');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::livewire('/profile', Livewire\Profile::class)->name('profile');
    Route::post('/logout', LogoutController::class)->name('logout');

    // İstifadəçilər və Rollar (spec §9)
    Route::livewire('/roles', Livewire\Roles::class)->name('roles.index')->can('role_read');
    Route::livewire('/roles/create', Livewire\RoleEditor::class)->name('roles.create')->can('role_create');
    Route::livewire('/roles/{role}/edit', Livewire\RoleEditor::class)->name('roles.edit')->can('role_update');
    Route::livewire('/designations', Livewire\Designations::class)->name('designations.index')->can('designation_read');
    Route::livewire('/departments', Livewire\Departments::class)->name('departments.index')->can('department_read');
    Route::livewire('/users', Livewire\Users::class)->name('users.index')->can('user_read');
    Route::livewire('/users/{user}/permissions', Livewire\UserPermissions::class)->name('users.permissions')->can('permission_update');
});
