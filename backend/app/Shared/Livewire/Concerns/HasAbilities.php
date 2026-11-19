<?php

namespace App\Shared\Livewire\Concerns;

use Illuminate\Support\Facades\Gate;

/**
 * Maps a page's actions to spec permission keys, e.g. ['create' => 'hub_create', ...].
 * Views hide buttons with $this->can('create'); actions re-check on the server.
 */
trait HasAbilities
{
    /** @return array<string, string> */
    abstract protected function abilities(): array;

    public function can(string $action): bool
    {
        return Gate::allows($this->abilities()[$action] ?? '__none__');
    }

    protected function authorizeAction(string $action): void
    {
        Gate::authorize($this->abilities()[$action] ?? '__none__');
    }
}
