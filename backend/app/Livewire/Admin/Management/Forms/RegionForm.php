<?php

namespace App\Livewire\Admin\Management\Forms;

use App\Models\Region;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Form;

/** Spec §7.3 region form: name, zone, status. */
class RegionForm extends Form
{
    #[Locked]
    public ?int $id = null;

    public string $name = '';

    public string $zone_id = '';

    public bool $status = true;

    public function setRecord(?int $id, ?string $zoneId = null): void
    {
        $this->reset();
        $this->zone_id = (string) $zoneId;

        if ($id === null) {
            return;
        }

        $region = Region::findOrFail($id);
        $this->id = $region->id;
        $this->name = $region->name;
        $this->zone_id = (string) $region->zone_id;
        $this->status = $region->status;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100',
                Rule::unique('regions', 'name')->where('zone_id', (int) $this->zone_id)->ignore($this->id)],
            'zone_id' => ['required', 'integer', 'exists:zones,id'],
            'status' => ['boolean'],
        ];
    }

    protected function validationAttributes(): array
    {
        return ['name' => 'Ad', 'zone_id' => 'Zona'];
    }

    public function save(): string
    {
        $data = $this->validate();

        if ($this->id) {
            Region::findOrFail($this->id)->update($data);

            return 'Region yeniləndi.';
        }

        Region::create($data);

        return 'Region əlavə edildi.';
    }
}
