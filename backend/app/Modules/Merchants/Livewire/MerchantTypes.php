<?php

namespace App\Modules\Merchants\Livewire;

use App\Modules\Merchants\Livewire\Forms\MerchantTypeForm;
use App\Modules\Merchants\Models\MerchantType;
use App\Shared\Livewire\Concerns\ConfirmsDeletion;
use App\Shared\Livewire\Concerns\HasAbilities;
use App\Shared\Livewire\Concerns\ManagesFormModal;
use App\Shared\Livewire\Concerns\Toasts;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

/** Satıcı tipləri (spec §6.6): # · Başlıq · İstifadəçilər · Əməliyyatlar; filters q and "only with merchants". */
#[Title('Partnyor növləri')]
class MerchantTypes extends Component
{
    use ConfirmsDeletion, HasAbilities, ManagesFormModal, Toasts;

    public MerchantTypeForm $form;

    #[Url(except: '')]
    public string $q = '';

    #[Url(as: 'has_users', except: false)]
    public bool $hasMerchants = false;

    public function resetFilters(): void
    {
        $this->reset('q', 'hasMerchants');
    }

    // The spec has no keys of its own for merchant types; they follow the merchant keys.
    protected function abilities(): array
    {
        return ['create' => 'merchant_create', 'update' => 'merchant_update', 'delete' => 'merchant_delete'];
    }

    protected function deletableQuery(): Builder
    {
        return MerchantType::query();
    }

    public function render()
    {
        return view('merchants::merchant-types', [
            'types' => MerchantType::query()
                ->withCount('merchants')
                ->when($this->q !== '', fn ($q) => $q->whereLike('title', "%{$this->q}%"))
                ->when($this->hasMerchants, fn ($q) => $q->has('merchants'))
                ->orderBy('title')
                ->get(),
        ]);
    }
}
