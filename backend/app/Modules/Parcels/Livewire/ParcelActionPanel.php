<?php

namespace App\Modules\Parcels\Livewire;

use App\Modules\Couriers\Models\Courier;
use App\Modules\Network\Models\Hub;
use App\Modules\Network\Models\RejectReason;
use App\Modules\Parcels\Models\Parcel;
use App\Modules\Parcels\Services\ParcelWorkflow;
use App\Modules\Parcels\Workflow\ParcelActions;
use App\Shared\Livewire\Concerns\Toasts;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Status change dialog (spec §3, §4.1): for one parcel (only the actions its status allows, plus undo)
 * or in bulk, where tracking codes are added by scanning (Enter) or from the selected rows. Bulk applies
 * each parcel separately and lists the ones that failed.
 */
class ParcelActionPanel extends Component
{
    use Toasts;

    public bool $open = false;

    public ?int $parcelId = null;

    /** @var list<string> */
    public array $trackingIds = [];

    public string $scan = '';

    public string $action = '';

    /** @var array<string, mixed> */
    public array $input = [];

    /** @var array<string, string> */
    public array $failed = [];

    #[On('parcel-action')]
    public function openFor(int $parcelId): void
    {
        Gate::authorize('parcel_status_update');
        $this->resetPanel();
        $this->parcelId = $parcelId;
        $this->action = array_key_first(ParcelActions::availableFor(Parcel::findOrFail($parcelId)->status)) ?? '';
        $this->open = true;
    }

    /** @param list<string> $trackingIds */
    #[On('parcel-bulk')]
    public function openBulk(array $trackingIds = []): void
    {
        Gate::authorize('parcel_status_update');
        $this->resetPanel();
        $this->trackingIds = array_values(array_unique($trackingIds));
        $this->action = 'assign_delivery';
        $this->open = true;
    }

    /** Barcode scanners type the code and press Enter (spec §5 "Skaner prinsipi"). */
    public function addScan(): void
    {
        $code = strtoupper(trim($this->scan));
        $this->scan = '';

        if ($code === '') {
            return;
        }

        if (! Parcel::where('tracking_id', $code)->exists()) {
            $this->dispatch('scan-result', ok: false);
            $this->toast("{$code}: paket tapılmadı.", 'error');

            return;
        }

        $this->trackingIds = array_values(array_unique([...$this->trackingIds, $code]));
        $this->dispatch('scan-result', ok: true);
    }

    public function removeTracking(string $code): void
    {
        $this->trackingIds = array_values(array_diff($this->trackingIds, [$code]));
    }

    public function updatedAction(): void
    {
        $this->input = ['sms' => []];
        $this->resetValidation();
    }

    public function submit(ParcelWorkflow $workflow): void
    {
        Gate::authorize('parcel_status_update');
        $this->failed = [];
        $input = array_map(fn ($v) => $v === '' ? null : $v, $this->input);

        if ($this->parcelId) {
            $workflow->apply(Parcel::findOrFail($this->parcelId), $this->action, $input, auth()->id());
            $this->finish('Status yeniləndi.');

            return;
        }

        $result = $workflow->bulk($this->trackingIds, $this->action, $input, auth()->id());
        $this->failed = $result['failed'];
        $this->trackingIds = array_keys($result['failed']);
        $this->dispatch('parcels-updated');
        $this->toast(count($result['done']).' paket yeniləndi'.($result['failed'] ? ', '.count($result['failed']).' alınmadı.' : '.'), $result['failed'] ? 'error' : 'success');

        if (! $result['failed']) {
            $this->open = false;
        }
    }

    public function undo(ParcelWorkflow $workflow): void
    {
        Gate::authorize('parcel_status_update');
        $workflow->undo(Parcel::findOrFail($this->parcelId), auth()->id());
        $this->finish('Son əməliyyat geri alındı.');
    }

    private function finish(string $message): void
    {
        $this->open = false;
        $this->dispatch('parcels-updated');
        $this->toast($message);
    }

    private function resetPanel(): void
    {
        $this->reset('parcelId', 'trackingIds', 'scan', 'action', 'failed');
        $this->input = ['sms' => []];
        $this->resetValidation();
    }

    public function render()
    {
        $parcel = $this->parcelId ? Parcel::find($this->parcelId) : null;
        $actions = $parcel ? ParcelActions::availableFor($parcel->status) : array_map(fn ($a) => $a['label'], ParcelActions::all());
        $definition = $this->action !== '' && isset(ParcelActions::all()[$this->action]) ? ParcelActions::get($this->action) : null;

        return view('parcels::action-panel', [
            'parcel' => $parcel,
            'actions' => $actions,
            'definition' => $definition,
            'couriers' => $definition && array_intersect(['delivery_man_id'], $definition['fields'])
                ? Courier::query()->with('user:id,name')->whereHas('user', fn ($q) => $q->where('status', true))->get()->sortBy('user.name')
                : collect(),
            'hubs' => Hub::query()->where('status', true)->orderBy('name')->get(['id', 'name']),
            'reasons' => RejectReason::query()->where('status', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
