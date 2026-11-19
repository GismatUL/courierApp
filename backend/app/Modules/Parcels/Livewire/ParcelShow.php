<?php

namespace App\Modules\Parcels\Livewire;

use App\Modules\Parcels\Models\Parcel;
use App\Modules\Parcels\Workflow\ParcelActions;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

/** Paket detalları + jurnal + çatdırılma sübutu (spec §4.3, §4.4, §4.5). */
class ParcelShow extends Component
{
    #[Locked]
    public int $parcelId;

    public function mount(Parcel $parcel): void
    {
        Gate::authorize('parcel_read');
        $this->parcelId = $parcel->id;
    }

    #[On('parcels-updated')]
    public function refreshParcel(): void {}

    public function render()
    {
        $parcel = Parcel::with([
            'merchant', 'shop', 'zone', 'packageType', 'creator:id,name', 'deliveryMan.user:id,name,mobile', 'pickupman.user:id,name',
            'hub:id,name', 'shelf:id,name,code', 'rejectReason', 'proof',
            'events' => fn ($q) => $q->with(['actor:id,name,email', 'deliveryMan.user:id,name', 'hub:id,name']),
        ])->findOrFail($this->parcelId);

        return view('parcels::parcel-show', [
            'parcel' => $parcel,
            'actionLabels' => ['create' => 'Paket yaradıldı', 'undo' => 'Geri alındı'] + array_map(fn ($a) => $a['label'], ParcelActions::all()) + ['shelf' => 'Qutulanma', 'zone' => 'Zonalama', 'address_confirmed' => 'Ünvan təsdiqləndi'],
            'stages' => ['Gözləyir', 'İcra edilir', 'Anbar', 'Kuryer təyin edilmişdir', 'Çatdırıldı'],
            'canAct' => Gate::allows('parcel_status_update') && ParcelActions::availableFor($parcel->status) !== [],
        ])->title("Paket {$parcel->tracking_id}");
    }
}
