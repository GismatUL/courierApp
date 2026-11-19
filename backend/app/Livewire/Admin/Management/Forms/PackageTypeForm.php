<?php

namespace App\Livewire\Admin\Management\Forms;

use App\Models\PackageType;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Form;

/** Spec §7.4: name, min/max weight (kg), description, status. Ranges must not overlap (spec recommendation). */
class PackageTypeForm extends Form
{
    public ?int $id = null;

    public string $name = '';

    public string $min_weight = '0';

    public string $max_weight = '';

    public string $description = '';

    public bool $status = true;

    public function setRecord(?int $id): void
    {
        $this->reset();

        if ($id === null) {
            return;
        }

        $type = PackageType::findOrFail($id);
        $this->id = $type->id;
        $this->name = $type->name;
        $this->min_weight = (string) (float) $type->min_weight;
        $this->max_weight = $type->max_weight === null ? '' : (string) (float) $type->max_weight;
        $this->description = (string) $type->description;
        $this->status = $type->status;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('package_types', 'name')->ignore($this->id)],
            'min_weight' => ['required', 'numeric', 'min:0', 'max:99999'],
            'max_weight' => ['nullable', 'numeric', 'gt:min_weight', 'max:99999'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['boolean'],
        ];
    }

    protected function validationAttributes(): array
    {
        return ['name' => 'Ad', 'min_weight' => 'Minimal çəki', 'max_weight' => 'Maksimal çəki', 'description' => 'Təsvir'];
    }

    public function save(): string
    {
        $data = $this->validate();

        if ($data['status'] && $clash = $this->overlappingType()) {
            throw ValidationException::withMessages([
                'form.min_weight' => "Çəki aralığı \"{$clash->name}\" ({$clash->weightRange()}) ilə üst-üstə düşür.",
            ]);
        }

        $data['max_weight'] = $data['max_weight'] === '' ? null : $data['max_weight'];
        $data['description'] = $data['description'] ?: null;

        if ($this->id) {
            PackageType::findOrFail($this->id)->update($data);

            return 'Paket növü yeniləndi.';
        }

        PackageType::create($data);

        return 'Paket növü əlavə edildi.';
    }

    /** Ranges are [min, max); touching ends (0–0.5 and 0.5–10) are allowed, a null max is open-ended. */
    private function overlappingType(): ?PackageType
    {
        $min = (float) $this->min_weight;
        $max = $this->max_weight === '' ? null : (float) $this->max_weight;

        return PackageType::query()
            ->where('status', true)
            ->when($this->id, fn ($q) => $q->whereKeyNot($this->id))
            ->get()
            ->first(fn (PackageType $other) => ($max === null || (float) $other->min_weight < $max)
                && ($other->max_weight === null || $min < (float) $other->max_weight));
    }
}
