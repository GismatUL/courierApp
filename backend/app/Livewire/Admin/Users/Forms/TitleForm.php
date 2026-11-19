<?php

namespace App\Livewire\Admin\Users\Forms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Form;

/** Title + status form shared by designations and departments (spec §9.2). */
class TitleForm extends Form
{
    /**
     * Protected so it is never sent to (or accepted from) the browser; the page sets it on every request.
     *
     * @var class-string<Model>
     */
    protected string $model = '';

    #[Locked]
    public ?int $id = null;

    /** @param class-string<Model> $model */
    public function useModel(string $model): void
    {
        $this->model = $model;
    }

    public string $title = '';

    public bool $status = true;

    public function setRecord(?int $id): void
    {
        $this->reset('id', 'title', 'status');

        if ($id === null) {
            return;
        }

        $record = $this->model::findOrFail($id);
        $this->id = $record->id;
        $this->title = $record->title;
        $this->status = $record->status;
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:100', Rule::unique((new $this->model)->getTable(), 'title')->ignore($this->id)],
            'status' => ['boolean'],
        ];
    }

    protected function validationAttributes(): array
    {
        return ['title' => 'Başlıq'];
    }

    public function save(): string
    {
        $data = $this->validate();

        $this->id ? $this->model::findOrFail($this->id)->update($data) : $this->model::create($data);

        return 'Yadda saxlanıldı.';
    }
}
