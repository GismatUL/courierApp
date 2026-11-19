<?php

namespace App\Providers;

use App\Modules\Access\Models\User;
use App\Shared\View\Sidebar;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/** Cross-module wiring only; each module has its own provider in app/Modules/<Name>. */
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Every spec permission key is a Gate ability: @can('hub_create'), ->can('hub_read') on routes,
        // Gate::authorize() in components. Unknown abilities fall through.
        Gate::before(fn (User $user, string $ability) => $user->hasPermission($ability) ? true : null);

        Blade::component('admin.sidebar', Sidebar::class);
    }
}
