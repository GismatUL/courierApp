<?php

namespace App\Livewire\Admin\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Delete with a confirmation dialog (<x-admin.confirm-delete />). A record can refuse deletion
 * by returning a reason from deletionBlocker(), e.g. when other data still points to it.
 * The component also uses the HasAbilities and Toasts traits.
 */
trait ConfirmsDeletion
{
    public ?int $deletingId = null;

    abstract protected function deletableQuery(): Builder;

    public function confirmDelete(int $id): void
    {
        $this->authorizeAction('delete');
        $this->deletingId = $id;
    }

    public function cancelDelete(): void
    {
        $this->deletingId = null;
    }

    public function delete(): void
    {
        $this->authorizeAction('delete');
        $record = $this->deletableQuery()->findOrFail($this->deletingId);
        $this->deletingId = null;

        if ($reason = $record->deletionBlocker()) {
            $this->toast($reason, 'error');

            return;
        }

        $record->delete();
        $this->toast('Silindi.');
    }
}
