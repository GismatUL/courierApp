<?php

namespace App\Shared\Providers;

use App\Shared\Support\Permissions;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

/**
 * Base provider for a module in app/Modules/<Name>. By convention it loads, when present:
 *   routes.php          admin routes (prefix /admin, name "admin.", "web" middleware)
 *   resources/views     as view namespace "<name>::" (anonymous components as <x-<name>::…>)
 *   Database/Migrations
 *   permissions.php     the module's rows in the permission matrix
 */
abstract class ModuleServiceProvider extends ServiceProvider
{
    /** Folder name under app/Modules, e.g. "Network". */
    abstract protected function module(): string;

    /** @var list<class-string> Artisan commands of the module. */
    protected array $commands = [];

    protected function path(string $relative = ''): string
    {
        return app_path('Modules/'.$this->module().($relative ? '/'.$relative : ''));
    }

    protected function key(): string
    {
        return Str::kebab($this->module());
    }

    public function register(): void
    {
        if (is_file($file = $this->path('permissions.php'))) {
            Permissions::register(require $file);
        }
    }

    public function boot(): void
    {
        if (is_dir($views = $this->path('resources/views'))) {
            $this->loadViewsFrom($views, $this->key());
            Blade::anonymousComponentPath($views.'/components', $this->key());
        }

        if (is_dir($migrations = $this->path('Database/Migrations'))) {
            $this->loadMigrationsFrom($migrations);
        }

        if (is_file($routes = $this->path('routes.php')) && ! $this->app->routesAreCached()) {
            Route::middleware('web')->prefix('admin')->name('admin.')->group($routes);
        }

        if ($this->commands && $this->app->runningInConsole()) {
            $this->commands($this->commands);
        }
    }
}
