<?php

namespace App\Livewire\Admin\Management\Forms;

use App\Models\Shelf;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Form;

/** Spec §7.6 form: name, code (scanned at the warehouse, so unique), zone, limit, status. */
class ShelfForm extends Form
{
    #[Locked]
    public ?int $id = null;

    public string $name = '';

    public string $code = '';

    public string $zone_id = '';

    public string $limit = '';

    public bool $status = true;

    public function setRecord(?int $id): void
    {
        $this->reset();

        if ($id === null) {
            return;
        }

        $shelf = Shelf::findOrFail($id);
        $this->id = $shelf->id;
        $this->name = $shelf->name;
        $this->code = $shelf->code;
        $this->zone_id = (string) $shelf->zone_id;
        $this->limit = $shelf->limit === null ? '' : (string) $shelf->limit;
        $this->status = $shelf->status;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:30', 'regex:/^[A-Za-z0-9_-]+$/', Rule::unique('shelves', 'code')->ignore($this->id)],
            'zone_id' => ['required', 'integer', 'exists:zones,id'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'status' => ['boolean'],
        ];
    }

    protected function messages(): array
    {
        return ['code.regex' => 'Kod yalnız hərf, rəqəm, "-" və "_" ola bilər (barkod oxuducu üçün).'];
    }

    protected function validationAttributes(): array
    {
        return ['name' => 'Ad', 'code' => 'Kod', 'zone_id' => 'Zona', 'limit' => 'Limit'];
    }

    public function save(): string
    {
        $data = $this->validate();
        $data['code'] = strtoupper($data['code']);
        $data['limit'] = $data['limit'] === '' ? null : (int) $data['limit'];

        if ($this->id) {
            Shelf::findOrFail($this->id)->update($data);

            return 'Qutu yeniləndi.';
        }

        Shelf::create($data);

        return 'Qutu əlavə edildi.';
    }
}
