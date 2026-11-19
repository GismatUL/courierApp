<?php

namespace App\Modules\Merchants\Livewire\Forms;

use App\Modules\Merchants\Models\MerchantDeliveryCharge;
use App\Modules\Network\Enums\TariffType;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Form;

/** Spec §6.2: zone, package type, tariff type, tracking/delivery time (text), price; unique per merchant. */
class ChargeForm extends Form
{
    /** Set by the page on every request; never taken from the browser. */
    protected int $merchantId = 0;

    #[Locked]
    public ?int $id = null;

    public string $zone_id = '';

    public string $package_type_id = '';

    public string $tariff_type = '';

    public string $tracking_time = '';

    public string $delivery_time = '';

    public string $price = '';

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

        $charge = MerchantDeliveryCharge::where('merchant_id', $this->merchantId)->findOrFail($id);
        $this->id = $charge->id;
        $this->zone_id = (string) $charge->zone_id;
        $this->package_type_id = (string) $charge->package_type_id;
        $this->tariff_type = $charge->tariff_type->value;
        $this->tracking_time = (string) $charge->tracking_time;
        $this->delivery_time = (string) $charge->delivery_time;
        $this->price = (string) (float) $charge->price;
    }

    protected function rules(): array
    {
        return [
            'zone_id' => ['required', 'integer', 'exists:zones,id'],
            'package_type_id' => ['required', 'integer', 'exists:package_types,id'],
            'tariff_type' => ['required', Rule::enum(TariffType::class),
                Rule::unique('merchant_delivery_charges')->where(fn ($q) => $q
                    ->where('merchant_id', $this->merchantId)
                    ->where('zone_id', (int) $this->zone_id)
                    ->where('package_type_id', (int) $this->package_type_id))
                    ->ignore($this->id)],
            'tracking_time' => ['nullable', 'string', 'max:50'],
            'delivery_time' => ['nullable', 'string', 'max:50'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999', 'decimal:0,2'],
        ];
    }

    protected function messages(): array
    {
        return ['tariff_type.unique' => 'Bu zona, paket növü və xidmət növü üçün qiymət artıq var.'];
    }

    protected function validationAttributes(): array
    {
        return ['zone_id' => 'Zona', 'package_type_id' => 'Paket növü', 'tariff_type' => 'Xidmət növü', 'price' => 'Qiymət',
            'tracking_time' => 'Daşınma müddəti', 'delivery_time' => 'Götürülmə müddəti'];
    }

    public function save(): string
    {
        $data = $this->validate();
        $data['tracking_time'] = $data['tracking_time'] ?: null;
        $data['delivery_time'] = $data['delivery_time'] ?: null;

        if ($this->id) {
            MerchantDeliveryCharge::where('merchant_id', $this->merchantId)->findOrFail($this->id)->update($data);

            return 'Tarif yeniləndi.';
        }

        MerchantDeliveryCharge::create($data + ['merchant_id' => $this->merchantId]);

        return 'Tarif əlavə edildi.';
    }
}
