<?php

namespace App\Modules\Merchants\Livewire;

use App\Modules\Merchants\Livewire\Forms\ChargeForm;
use App\Modules\Merchants\Models\MerchantDeliveryCharge;
use App\Modules\Network\Enums\TariffType;
use App\Modules\Network\Models\PackageType;
use App\Modules\Network\Models\Zone;

/** "Çatdırılma Ödənişi" tab (spec §6.2): # · Zona · Paket növü · Xidmət növü · Daşınma müddəti · Götürülmə müddəti · Qiymət. */
class MerchantCharges extends MerchantTab
{
    public ChargeForm $form;

    protected function model(): string
    {
        return MerchantDeliveryCharge::class;
    }

    protected function abilities(): array
    {
        return ['create' => 'merchant_delivery_charge_create', 'update' => 'merchant_delivery_charge_update', 'delete' => 'merchant_delivery_charge_delete'];
    }

    public function render()
    {
        return view('merchants::tabs.charges', [
            'charges' => MerchantDeliveryCharge::query()
                ->where('merchant_id', $this->merchantId)
                ->with(['zone:id,name', 'packageType:id,name,min_weight'])
                ->get()
                ->sortBy(fn ($c) => [$c->zone->name, (float) $c->packageType->min_weight, $c->tariff_type->value]),
            'zones' => Zone::query()->where('status', true)->orderBy('name')->get(['id', 'name']),
            'packageTypes' => PackageType::query()->where('status', true)->orderBy('min_weight')->get(['id', 'name']),
            'tariffs' => TariffType::cases(),
        ]);
    }
}
