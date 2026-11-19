<?php

namespace App\Modules\Merchants\Livewire\Forms;

use App\Modules\Merchants\Enums\PaymentMethod;
use App\Modules\Merchants\Models\MerchantPaymentAccount;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Form;

/** Spec §6.4: payment method (Bank / Mobil / Nağd) and the fields that method needs. */
class PaymentAccountForm extends Form
{
    /** Set by the page on every request; never taken from the browser. */
    protected int $merchantId = 0;

    #[Locked]
    public ?int $id = null;

    public string $method = 'bank';

    /** @var array<string, string> */
    public array $details = [];

    public bool $status = true;

    public function forMerchant(int $merchantId): void
    {
        $this->merchantId = $merchantId;
    }

    public function setRecord(?int $id): void
    {
        $this->reset();

        if ($id === null) {
            return;
        }

        $account = MerchantPaymentAccount::where('merchant_id', $this->merchantId)->findOrFail($id);
        $this->id = $account->id;
        $this->method = $account->method->value;
        $this->details = array_map('strval', $account->details);
        $this->status = $account->status;
    }

    protected function rules(): array
    {
        $rules = ['method' => ['required', Rule::enum(PaymentMethod::class)], 'status' => ['boolean']];

        foreach (PaymentMethod::tryFrom($this->method)?->fields() ?? [] as $key => $field) {
            $rules["details.{$key}"] = $field['rules'];
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'details.iban.regex' => 'IBAN AZ ilə başlayan 28 simvol olmalıdır (boşluqsuz).',
            'details.number.regex' => 'Nömrə 994XXXXXXXXX formatında olmalıdır.',
        ];
    }

    protected function validationAttributes(): array
    {
        return collect(PaymentMethod::tryFrom($this->method)?->fields() ?? [])
            ->mapWithKeys(fn ($field, $key) => ["details.{$key}" => $field['label']])
            ->put('method', 'Ödəniş metodu')
            ->all();
    }

    public function save(): string
    {
        if (isset($this->details['iban'])) {
            $this->details['iban'] = strtoupper(str_replace(' ', '', $this->details['iban']));
        }

        $data = $this->validate();
        $method = PaymentMethod::from($data['method']);
        // Keep only the current method's fields, so switching method drops stale details.
        $details = array_intersect_key($data['details'] ?? [], $method->fields());
        $values = ['method' => $method, 'details' => array_filter($details, fn ($v) => $v !== null && $v !== ''), 'status' => $data['status']];

        if ($this->id) {
            MerchantPaymentAccount::where('merchant_id', $this->merchantId)->findOrFail($this->id)->update($values);

            return 'Ödəniş hesabı yeniləndi.';
        }

        MerchantPaymentAccount::create($values + ['merchant_id' => $this->merchantId]);

        return 'Ödəniş hesabı əlavə edildi.';
    }
}
