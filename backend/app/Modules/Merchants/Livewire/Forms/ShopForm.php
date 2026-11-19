<?php

namespace App\Modules\Merchants\Livewire\Forms;

use App\Modules\Merchants\Models\Merchant;
use App\Modules\Merchants\Models\MerchantShop;
use App\Modules\Merchants\Services\MerchantService;
use Livewire\Attributes\Locked;
use Livewire\Form;

/** Spec §6.3 POST /admin/merchant/shops/store: name, contact_no, address* + lat* + long*, status. */
class ShopForm extends Form
{
    /** Set by the page on every request; never taken from the browser. */
    protected int $merchantId = 0;

    #[Locked]
    public ?int $id = null;

    public string $name = '';

    public string $contact_no = '';

    public string $address = '';

    public string $latitude = '';

    public string $longitude = '';

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

        $shop = MerchantShop::where('merchant_id', $this->merchantId)->findOrFail($id);
        $this->id = $shop->id;
        $this->name = $shop->name;
        $this->contact_no = (string) $shop->contact_no;
        $this->address = $shop->address;
        $this->latitude = (string) $shop->latitude;
        $this->longitude = (string) $shop->longitude;
        $this->status = $shop->status;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'contact_no' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9 ()-]{7,20}$/'],
            'address' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'status' => ['boolean'],
        ];
    }

    protected function validationAttributes(): array
    {
        return ['name' => 'Ad', 'contact_no' => 'Əlaqə', 'address' => 'Ünvan', 'latitude' => 'Enlik', 'longitude' => 'Uzunluq'];
    }

    public function save(): string
    {
        $data = $this->validate();
        $data['contact_no'] = $data['contact_no'] ?: null;

        $merchant = Merchant::findOrFail($this->merchantId);
        $shop = $this->id ? MerchantShop::where('merchant_id', $this->merchantId)->findOrFail($this->id) : null;
        app(MerchantService::class)->saveShop($merchant, $shop, $data);

        return $this->id ? 'Yığma nöqtəsi yeniləndi.' : 'Yığma nöqtəsi əlavə edildi.';
    }
}
