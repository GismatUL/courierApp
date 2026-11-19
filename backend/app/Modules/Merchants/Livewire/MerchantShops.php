<?php

namespace App\Modules\Merchants\Livewire;

use App\Modules\Merchants\Livewire\Forms\ShopForm;
use App\Modules\Merchants\Models\MerchantShop;
use App\Modules\Merchants\Services\MerchantService;
use Illuminate\Database\Eloquent\Model;

/** "Filiallar" tab (spec §6.3): pickup points; one is the default ("Təyin edilmiş"). */
class MerchantShops extends MerchantTab
{
    public ShopForm $form;

    protected function model(): string
    {
        return MerchantShop::class;
    }

    protected function abilities(): array
    {
        return ['create' => 'merchant_shop_create', 'update' => 'merchant_shop_update', 'delete' => 'merchant_shop_delete'];
    }

    public function makeDefault(int $id, MerchantService $service): void
    {
        $this->authorizeAction('update');
        $service->makeDefault($this->deletableQuery()->findOrFail($id));
        $this->toast('Əsas yığma nöqtəsi dəyişdirildi.');
    }

    protected function performDelete(Model $record): void
    {
        app(MerchantService::class)->deleteShop($record);
    }

    public function render()
    {
        return view('merchants::tabs.shops', [
            'shops' => MerchantShop::query()->where('merchant_id', $this->merchantId)->orderByDesc('is_default')->orderBy('name')->get(),
        ]);
    }
}
