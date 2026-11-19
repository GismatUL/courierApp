<?php

namespace App\Modules\Merchants\Livewire;

use App\Shared\Livewire\Concerns\ConfirmsDeletion;
use App\Shared\Livewire\Concerns\HasAbilities;
use App\Shared\Livewire\Concerns\ManagesFormModal;
use App\Shared\Livewire\Concerns\Toasts;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Base for a CRUD tab on the merchant card. The merchant id is locked, and every lookup is scoped to it,
 * so a record id sent from the browser can never reach another merchant's data.
 */
abstract class MerchantTab extends Component
{
    use ConfirmsDeletion, HasAbilities, ManagesFormModal, Toasts;

    #[Locked]
    public int $merchantId;

    /** @return class-string<Model> */
    abstract protected function model(): string;

    /** Runs on every follow-up request, after the locked merchant id is restored. */
    public function hydrate(): void
    {
        $this->form->forMerchant($this->merchantId);
    }

    public function mount(int $merchantId): void
    {
        $this->merchantId = $merchantId;
        $this->form->forMerchant($merchantId);
    }

    protected function deletableQuery(): Builder
    {
        return $this->model()::query()->where('merchant_id', $this->merchantId);
    }
}
