<?php

namespace App\Livewire\Admin\Concerns;

/** Shows a toast in the admin layout (listens for the browser event "toast"). */
trait Toasts
{
    protected function toast(string $message, string $type = 'success'): void
    {
        $this->dispatch('toast', message: $message, type: $type);
    }
}
