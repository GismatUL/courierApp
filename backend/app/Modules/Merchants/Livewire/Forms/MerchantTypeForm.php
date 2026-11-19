<?php

namespace App\Modules\Merchants\Livewire\Forms;

use App\Modules\Merchants\Models\MerchantType;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Form;

/** Spec §6.6: title (required). */
class MerchantTypeForm extends Form
{
    #[Locked]
    public ?int $id = null;

    public string $title = '';

    public function setRecord(?int $id): void
    {
        $this->reset();

        if ($id !== null) {
            $type = MerchantType::findOrFail($id);
            $this->id = $type->id;
            $this->title = $type->title;
        }
    }

    protected function rules(): array
    {
        return ['title' => ['required', 'string', 'max:100', Rule::unique('merchant_types', 'title')->ignore($this->id)]];
    }

    protected function validationAttributes(): array
    {
        return ['title' => 'Başlıq'];
    }

    public function save(): string
    {
        $data = $this->validate();
        $this->id ? MerchantType::findOrFail($this->id)->update($data) : MerchantType::create($data);

        return 'Yadda saxlanıldı.';
    }
}
