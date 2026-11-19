<?php

namespace App\Modules\Parcels\Livewire;

use App\Modules\Access\Models\User;
use App\Modules\Couriers\Models\Courier;
use App\Modules\Merchants\Models\Merchant;
use App\Modules\Merchants\Models\MerchantShop;
use App\Modules\Parcels\Enums\ParcelStatus;
use App\Modules\Parcels\Models\Parcel;
use App\Modules\Parcels\Services\ParcelFilters;
use App\Modules\Parcels\Services\ParcelService;
use App\Modules\Parcels\Workflow\ParcelActions;
use App\Shared\Livewire\Concerns\ConfirmsDeletion;
use App\Shared\Livewire\Concerns\HasAbilities;
use App\Shared\Livewire\Concerns\Toasts;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Paketlər (spec §4.1): filters (date range, status, merchant + shops, courier, invoice no, address,
 * search, creator), 100 rows per page, row actions, selection for bulk actions and label printing.
 */
#[Title('Paketlər')]
class Parcels extends Component
{
    use ConfirmsDeletion, HasAbilities, Toasts, WithPagination;

    #[Url(except: '')]
    public string $dateFrom = '';

    #[Url(except: '')]
    public string $dateTo = '';

    #[Url(except: '')]
    public string $status = '';

    #[Url(as: 'merchant', except: '')]
    public string $merchantId = '';

    /** @var list<string> */
    #[Url(as: 'shops', except: [])]
    public array $shopIds = [];

    #[Url(as: 'courier', except: '')]
    public string $courierId = '';

    #[Url(except: '')]
    public string $invoice = '';

    #[Url(except: '')]
    public string $address = '';

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'by', except: '')]
    public string $createdBy = '';

    /** @var list<int> */
    public array $selected = [];

    public function updated(string $property): void
    {
        if ($property === 'merchantId') {
            $this->shopIds = []; // spec §4.2 edge case: changing the merchant resets its shops
        }

        if ($property !== 'selected') {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset('dateFrom', 'dateTo', 'status', 'merchantId', 'shopIds', 'courierId', 'invoice', 'address', 'search', 'createdBy');
        $this->resetPage();
    }

    #[On('parcels-updated')]
    public function refreshList(): void
    {
        $this->selected = [];
    }

    public function bulk(): void
    {
        $ids = Parcel::whereIn('id', $this->selected)->pluck('tracking_id')->all();
        $this->dispatch('parcel-bulk', trackingIds: $ids);
    }

    public function printSelected()
    {
        abort_if($this->selected === [], 422);

        return $this->redirectRoute('admin.parcels.labels', ['ids' => implode(',', $this->selected)]);
    }

    protected function abilities(): array
    {
        return ['create' => 'parcel_create', 'update' => 'parcel_update', 'delete' => 'parcel_delete', 'status' => 'parcel_status_update'];
    }

    protected function deletableQuery(): Builder
    {
        return Parcel::query();
    }

    protected function performDelete(Model $record): void
    {
        app(ParcelService::class)->delete($record);
    }

    /** Same keys as the query string, so the Excel export link reproduces this list. */
    private function filters(): array
    {
        return [
            'dateFrom' => $this->dateFrom, 'dateTo' => $this->dateTo, 'status' => $this->status, 'merchant' => $this->merchantId,
            'shops' => $this->shopIds, 'courier' => $this->courierId, 'invoice' => $this->invoice, 'address' => $this->address,
            'q' => $this->search, 'by' => $this->createdBy,
        ];
    }

    public function render()
    {
        $parcels = ParcelFilters::apply($this->filters())
            ->with(['merchant:id,business_name,address', 'zone:id,name', 'deliveryMan.user:id,name', 'rejectReason:id,name'])
            ->orderByDesc('id')
            ->paginate(100);

        return view('parcels::parcels', [
            'parcels' => $parcels,
            'statuses' => ParcelStatus::cases(),
            'merchants' => Merchant::query()->orderBy('business_name')->get(['id', 'business_name']),
            'shops' => $this->merchantId !== '' ? MerchantShop::where('merchant_id', (int) $this->merchantId)->orderBy('name')->get(['id', 'name']) : collect(),
            'couriers' => Courier::query()->with('user:id,name')->get()->sortBy('user.name'),
            'creators' => User::query()->where('panel_access', true)->orderBy('name')->get(['id', 'name']),
            'actionsByStatus' => collect(ParcelStatus::cases())->mapWithKeys(fn ($s) => [$s->value => ParcelActions::availableFor($s)]),
        ]);
    }
}
