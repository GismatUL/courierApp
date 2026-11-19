<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Every spec permission key (config/permissions.php) is a Gate ability: @can('hub_create'),
        // ->can('hub_read') on routes, Gate::authorize() in components. Unknown abilities fall through.
        Gate::before(fn (User $user, string $ability) => $user->hasPermission($ability) ? true : null);
    }
}
