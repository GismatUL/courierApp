<?php

namespace App\Livewire\Admin\Management;

use App\Livewire\Admin\Concerns\ConfirmsDeletion;
use App\Livewire\Admin\Concerns\HasAbilities;
use App\Livewire\Admin\Concerns\ManagesFormModal;
use App\Livewire\Admin\Concerns\Toasts;
use App\Livewire\Admin\Management\Forms\RegionForm;
use App\Models\Region;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/** Regionlar — sub-areas of a zone (spec §7.3, reached from the zone list). */
#[Title('Regionlar')]
class Regions extends Component
{
    use ConfirmsDeletion, HasAbilities, ManagesFormModal, Toasts, WithPagination;

    public RegionForm $form;

    #[Url(as: 'zone', except: '')]
    public string $zoneId = '';

    public function updatedZoneId(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset('zoneId');
        $this->resetPage();
    }

    /** New regions default to the zone in the filter. */
    public function create(): void
    {
        $this->authorizeAction('create');
        $this->form->resetValidation();
        $this->form->setRecord(null, $this->zoneId);
        $this->showForm = true;
    }

    protected function abilities(): array
    {
        return ['create' => 'region_create', 'update' => 'region_edit', 'delete' => 'region_delete'];
    }

    protected function deletableQuery(): Builder
    {
        return Region::query();
    }

    public function render()
    {
        return view('livewire.admin.management.regions', [
            'regions' => Region::query()
                ->with('zone:id,name')
                ->when($this->zoneId !== '', fn ($q) => $q->where('zone_id', (int) $this->zoneId))
                ->orderBy('name')
                ->paginate(10),
            'zones' => Zone::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
