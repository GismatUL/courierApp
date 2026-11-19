<?php

namespace App\Modules\Merchants;

use App\Modules\Merchants\Livewire\MerchantCharges;
use App\Modules\Merchants\Livewire\MerchantPaymentAccounts;
use App\Modules\Merchants\Livewire\MerchantShops;
use App\Shared\Providers\ModuleServiceProvider;
use Livewire\Livewire;

/** Merchants with their own tariffs, pickup points, payment accounts and types (spec §6). */
class MerchantsServiceProvider extends ModuleServiceProvider
{
    protected function module(): string
    {
        return 'Merchants';
    }

    public function boot(): void
    {
        parent::boot();

        // Tabs embedded on the merchant card.
        Livewire::component('merchants-charges', MerchantCharges::class);
        Livewire::component('merchants-shops', MerchantShops::class);
        Livewire::component('merchants-payment-accounts', MerchantPaymentAccounts::class);
    }
}
