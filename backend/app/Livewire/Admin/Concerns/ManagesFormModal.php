<?php

namespace App\Livewire\Admin\Concerns;

/**
 * Create/edit in a modal for list pages. The component declares `public <X>Form $form;`
 * whose Form class implements setRecord(?int $id) and save(): string (the success message).
 * The component also uses the Toasts trait.
 */
trait ManagesFormModal
{
    public bool $showForm = false;

    public function create(): void
    {
        $this->openForm(null);
    }

    public function edit(int $id): void
    {
        $this->openForm($id);
    }

    public function save(): void
    {
        $message = $this->form->save();
        $this->showForm = false;
        $this->toast($message);
    }

    private function openForm(?int $id): void
    {
        $this->form->resetValidation();
        $this->form->setRecord($id);
        $this->showForm = true;
    }
}
