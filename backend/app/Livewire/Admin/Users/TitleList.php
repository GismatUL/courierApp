<?php

namespace App\Livewire\Admin\Users;

use App\Livewire\Admin\Concerns\ConfirmsDeletion;
use App\Livewire\Admin\Concerns\HasAbilities;
use App\Livewire\Admin\Concerns\ManagesFormModal;
use App\Livewire\Admin\Concerns\Toasts;
use App\Livewire\Admin\Users\Forms\TitleForm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

/** Base for the simple "# · Başlıq · Status" lists (spec §9.2). */
abstract class TitleList extends Component
{
    use ConfirmsDeletion, HasAbilities, ManagesFormModal, Toasts;

    public TitleForm $form;

    /** @return class-string<Model> */
    abstract protected function model(): string;

    /** Permission prefix, e.g. "designation". */
    abstract protected function permissionPrefix(): string;

    abstract protected function description(): string;

    public function boot(): void
    {
        $this->form->useModel($this->model());
    }

    protected function abilities(): array
    {
        $prefix = $this->permissionPrefix();

        return ['create' => "{$prefix}_create", 'update' => "{$prefix}_update", 'delete' => "{$prefix}_delete"];
    }

    protected function deletableQuery(): Builder
    {
        return $this->model()::query();
    }

    public function render()
    {
        return view('livewire.admin.users.title-list', [
            'records' => $this->model()::query()->withCount('users')->orderBy('title')->get(),
            'description' => $this->description(),
        ]);
    }
}
