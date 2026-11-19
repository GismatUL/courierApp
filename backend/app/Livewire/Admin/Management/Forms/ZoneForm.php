<?php

namespace App\Livewire\Admin\Management\Forms;

use App\Models\Zone;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Form;

/** Spec §7.3 zone create form: name (required), note, hub, status. */
class ZoneForm extends Form
{
    #[Locked]
    public ?int $id = null;

    public string $name = '';

    public ?string $hub_id = null;

    public string $note = '';

    public bool $status = true;

    public function setRecord(?int $id): void
    {
        $this->reset();

        if ($id === null) {
            return;
        }

        $zone = Zone::findOrFail($id);
        $this->id = $zone->id;
        $this->name = $zone->name;
        $this->hub_id = $zone->hub_id ? (string) $zone->hub_id : null;
        $this->note = (string) $zone->note;
        $this->status = $zone->status;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100',
                Rule::unique('zones', 'name')->where('hub_id', $this->hub_id ?: null)->ignore($this->id)],
            'hub_id' => ['nullable', 'integer', 'exists:hubs,id'],
            'note' => ['nullable', 'string', 'max:1000'],
            'status' => ['boolean'],
        ];
    }

    protected function validationAttributes(): array
    {
        return ['name' => 'Ad', 'hub_id' => 'Filial', 'note' => 'Qeyd'];
    }

    public function save(): string
    {
        $data = $this->validate();
        $data['hub_id'] = $data['hub_id'] ?: null;
        $data['note'] = $data['note'] ?: null;

        if ($this->id) {
            Zone::findOrFail($this->id)->update($data);

            return 'Zona yeniləndi.';
        }

        Zone::create($data);

        return 'Zona əlavə edildi.';
    }
}
