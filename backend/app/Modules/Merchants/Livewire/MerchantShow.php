<?php

namespace App\Modules\Merchants\Livewire;

use App\Modules\Merchants\Models\Merchant;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Partnyor kartı (spec §6.1 /admin/merchant/view/{id}): header, key figures and tabs
 * Şirkət Məlumatı · Çatdırılma Ödənişi · Filiallar · Ödəniş Hesabı · Faktura.
 * Parcel counts, amounts and invoices are filled in once the Parcels and Finance modules exist.
 */
class MerchantShow extends Component
{
    public const TABS = [
        'info' => ['label' => 'Şirkət Məlumatı', 'ability' => 'merchant_view'],
        'charges' => ['label' => 'Çatdırılma Ödənişi', 'ability' => 'merchant_delivery_charge_read'],
        'shops' => ['label' => 'Filiallar', 'ability' => 'merchant_shop_read'],
        'payments' => ['label' => 'Ödəniş Hesabı', 'ability' => 'merchant_payment_read'],
        'invoices' => ['label' => 'Faktura', 'ability' => 'invoice_read'],
    ];

    #[Locked]
    public int $merchantId;

    #[Url(except: 'info')]
    public string $tab = 'info';

    public function mount(Merchant $merchant): void
    {
        Gate::authorize('merchant_view');
        $this->merchantId = $merchant->id;
    }

    public function render()
    {
        $merchant = Merchant::with(['hubs:id,name', 'type:id,title', 'defaultShop'])->findOrFail($this->merchantId);
        $tabs = array_filter(self::TABS, fn ($tab) => Gate::allows($tab['ability']));

        if (! array_key_exists($this->tab, $tabs)) {
            $this->tab = 'info';
        }

        return view('merchants::merchant-show', ['merchant' => $merchant, 'tabs' => $tabs])
            ->title($merchant->business_name);
    }
}
