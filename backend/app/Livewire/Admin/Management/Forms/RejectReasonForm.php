<?php

namespace App\Livewire\Admin\Management\Forms;

use App\Models\RejectReason;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Form;

/** Spec §7.7 form: name (required), status. */
class RejectReasonForm extends Form
{
    #[Locked]
    public ?int $id = null;

    public string $name = '';

    public bool $status = true;

    public function setRecord(?int $id): void
    {
        $this->reset();

        if ($id === null) {
            return;
        }

        $reason = RejectReason::findOrFail($id);
        $this->id = $reason->id;
        $this->name = $reason->name;
        $this->status = $reason->status;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('reject_reasons', 'name')->ignore($this->id)],
            'status' => ['boolean'],
        ];
    }

    protected function validationAttributes(): array
    {
        return ['name' => 'Ad'];
    }

    public function save(): string
    {
        $data = $this->validate();

        if ($this->id) {
            RejectReason::findOrFail($this->id)->update($data);

            return 'Səbəb yeniləndi.';
        }

        RejectReason::create($data);

        return 'Səbəb əlavə edildi.';
    }
}
