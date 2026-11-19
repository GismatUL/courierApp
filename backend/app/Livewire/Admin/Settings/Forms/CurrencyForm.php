<?php

namespace App\Livewire\Admin\Settings\Forms;

use App\Models\Currency;
use App\Services\Settings;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Form;

/** Spec §10.4 form: name, code, symbol (required), exchange rate "1 USD = ?" (required), symbol position, status. */
class CurrencyForm extends Form
{
    #[Locked]
    public ?int $id = null;

    public string $name = '';

    public string $code = '';

    public string $symbol = '';

    public string $exchange_rate = '';

    public string $position = 'after';

    public bool $status = true;

    public function setRecord(?int $id): void
    {
        $this->reset();

        if ($id === null) {
            return;
        }

        $currency = Currency::findOrFail($id);
        $this->id = $currency->id;
        $this->name = $currency->name;
        $this->code = $currency->code;
        $this->symbol = $currency->symbol;
        $this->exchange_rate = (string) (float) $currency->exchange_rate;
        $this->position = $currency->position;
        $this->status = $currency->status;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'regex:/^[A-Za-z]{3}$/', Rule::unique('currencies', 'code')->ignore($this->id)],
            'symbol' => ['required', 'string', 'max:10'],
            'exchange_rate' => ['required', 'numeric', 'gt:0', 'max:99999999'],
            'position' => ['required', Rule::in(['before', 'after'])],
            'status' => ['boolean'],
        ];
    }

    protected function messages(): array
    {
        return ['code.regex' => 'Kod 3 latın hərfi olmalıdır (ISO 4217, məs. AZN).'];
    }

    protected function validationAttributes(): array
    {
        return ['name' => 'Ad', 'code' => 'Kod', 'symbol' => 'Simvol', 'exchange_rate' => 'Mübadilə məzənnəsi', 'position' => 'Simvolun yeri'];
    }

    public function save(): string
    {
        $data = $this->validate();
        $data['code'] = strtoupper($data['code']);

        if ($this->id) {
            $currency = Currency::findOrFail($this->id);
            $default = app(Settings::class)->get('currency');

            if ($currency->code === $default && (! $data['status'] || $data['code'] !== $default)) {
                throw ValidationException::withMessages(['form.status' => 'Əsas valyutanı passiv etmək və ya kodunu dəyişmək olmaz. Əvvəlcə Ümumi Tənzimləmələrdə başqa valyuta seçin.']);
            }

            $currency->update($data);

            return 'Valyuta yeniləndi.';
        }

        Currency::create($data);

        return 'Valyuta əlavə edildi.';
    }
}
