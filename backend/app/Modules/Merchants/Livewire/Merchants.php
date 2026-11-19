<?php

namespace App\Modules\Merchants\Livewire;

use App\Modules\Merchants\Models\Merchant;
use App\Modules\Merchants\Services\MerchantService;
use App\Modules\Network\Models\Hub;
use App\Shared\Livewire\Concerns\ConfirmsDeletion;
use App\Shared\Livewire\Concerns\HasAbilities;
use App\Shared\Livewire\Concerns\Toasts;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/** Partnyorlar (spec §6.1): list, 10 per page; create/edit on MerchantEditor, the card on MerchantShow. */
#[Title('Partnyorlar')]
class Merchants extends Component
{
    use ConfirmsDeletion, HasAbilities, Toasts, WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'hub', except: '')]
    public string $hubId = '';

    #[Url(except: '')]
    public string $status = '';

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'hubId', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset('search', 'hubId', 'status');
        $this->resetPage();
    }

    protected function abilities(): array
    {
        return ['create' => 'merchant_create', 'update' => 'merchant_update', 'delete' => 'merchant_delete', 'view' => 'merchant_view'];
    }

    protected function deletableQuery(): Builder
    {
        return Merchant::query();
    }

    protected function performDelete(Model $record): void
    {
        app(MerchantService::class)->delete($record);
    }

    public function render()
    {
        $term = trim($this->search);

        return view('merchants::merchants', [
            'merchants' => Merchant::query()
                ->with('hubs:id,name')
                ->when($term !== '', fn ($q) => $q->where(fn ($q) => $q
                    ->whereLike('business_name', "%{$term}%")
                    ->orWhereLike('contact_name', "%{$term}%")
                    ->orWhereLike('email', "%{$term}%")
                    ->orWhereLike('mobile', "%{$term}%")
                    ->orWhere('unique_id', $term)))
                ->when($this->hubId !== '', fn ($q) => $q->whereHas('hubs', fn ($q) => $q->whereKey((int) $this->hubId)))
                ->when($this->status !== '', fn ($q) => $q->where('status', $this->status === '1'))
                ->orderBy('business_name')
                ->paginate(10),
            'hubs' => Hub::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
