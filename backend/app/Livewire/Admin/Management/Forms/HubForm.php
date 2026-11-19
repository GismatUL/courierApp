<?php

namespace App\Livewire\Admin\Management\Forms;

use App\Models\Hub;
use Illuminate\Validation\Rule;
use Livewire\Form;

/** Spec §7.1 form: name, contact person, phone, address + lat/long (required), status. */
class HubForm extends Form
{
    public ?int $id = null;

    public string $name = '';

    public string $phone = '';

    public string $address = '';

    public string $latitude = '';

    public string $longitude = '';

    public ?string $contact_user_id = null;

    public bool $status = true;

    public function setRecord(?int $id): void
    {
        $this->reset();

        if ($id === null) {
            return;
        }

        $hub = Hub::findOrFail($id);
        $this->id = $hub->id;
        $this->name = $hub->name;
        $this->phone = (string) $hub->phone;
        $this->address = $hub->address;
        $this->latitude = (string) $hub->latitude;
        $this->longitude = (string) $hub->longitude;
        $this->contact_user_id = $hub->contact_user_id ? (string) $hub->contact_user_id : null;
        $this->status = $hub->status;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('hubs', 'name')->ignore($this->id)],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9 ()-]{7,20}$/'],
            'address' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'contact_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['boolean'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'name' => 'Ad', 'phone' => 'Telefon', 'address' => 'Ünvan', 'latitude' => 'Enlik',
            'longitude' => 'Uzunluq', 'contact_user_id' => 'Əlaqədar şəxs',
        ];
    }

    public function save(): string
    {
        $data = $this->validate();
        $data['phone'] = $data['phone'] ?: null;
        $data['contact_user_id'] = $data['contact_user_id'] ?: null;

        if ($this->id) {
            Hub::findOrFail($this->id)->update($data);

            return 'Filial yeniləndi.';
        }

        Hub::create($data);

        return 'Filial əlavə edildi.';
    }
}
