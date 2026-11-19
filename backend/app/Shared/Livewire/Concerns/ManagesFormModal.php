<?php

namespace App\Shared\Livewire\Concerns;

/**
 * Create/edit in a modal for list pages. The component declares `public <X>Form $form;`
 * whose Form class implements setRecord(?int $id) and save(): string (the success message).
 * The component also uses the HasAbilities and Toasts traits.
 */
trait ManagesFormModal
{
    public bool $showForm = false;

    public function create(): void
    {
        $this->authorizeAction('create');
        $this->openForm(null);
    }

    public function edit(int $id): void
    {
        $this->authorizeAction('update');
        $this->openForm($id);
    }

    public function save(): void
    {
        $this->authorizeAction($this->form->id ? 'update' : 'create');
        $message = $this->form->save();
        $this->showForm = false;
        $this->toast($message);
    }

    protected function openForm(?int $id): void
    {
        $this->form->resetValidation();
        $this->form->setRecord($id);
        $this->showForm = true;
    }
}
