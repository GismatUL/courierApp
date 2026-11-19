<?php

namespace App\Modules\Merchants\Livewire;

use App\Modules\Merchants\Enums\PaymentMethod;
use App\Modules\Merchants\Livewire\Forms\PaymentAccountForm;
use App\Modules\Merchants\Models\MerchantPaymentAccount;

/** "Ödəniş Hesabı" tab (spec §6.4): # · Ödəniş Metodu · Hesab Məlumatı · Status. */
class MerchantPaymentAccounts extends MerchantTab
{
    public PaymentAccountForm $form;

    protected function model(): string
    {
        return MerchantPaymentAccount::class;
    }

    protected function abilities(): array
    {
        return ['create' => 'merchant_payment_create', 'update' => 'merchant_payment_update', 'delete' => 'merchant_payment_delete'];
    }

    public function render()
    {
        return view('merchants::tabs.payment-accounts', [
            'accounts' => MerchantPaymentAccount::query()->where('merchant_id', $this->merchantId)->orderBy('id')->get(),
            'methods' => PaymentMethod::cases(),
            'fields' => PaymentMethod::tryFrom($this->form->method)?->fields() ?? [],
        ]);
    }
}
