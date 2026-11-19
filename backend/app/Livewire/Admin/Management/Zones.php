<?php

namespace App\Livewire\Admin\Management;

use App\Livewire\Admin\Concerns\ConfirmsDeletion;
use App\Livewire\Admin\Concerns\HasAbilities;
use App\Livewire\Admin\Concerns\ManagesFormModal;
use App\Livewire\Admin\Concerns\Toasts;
use App\Livewire\Admin\Management\Forms\ZoneForm;
use App\Models\Hub;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/** Zonalar (spec §7.3). Editing a zone's prices happens on its tariff page. */
#[Title('Zonalar')]
class Zones extends Component
{
    use ConfirmsDeletion, HasAbilities, ManagesFormModal, Toasts, WithPagination;

    public ZoneForm $form;

    #[Url(as: 'hub', except: '')]
    public string $hubId = '';

    #[Url(as: 'q', except: '')]
    public string $search = '';

    public function updated(string $property): void
    {
        if (in_array($property, ['hubId', 'search'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset('hubId', 'search');
        $this->resetPage();
    }

    protected function abilities(): array
    {
        return ['create' => 'zone_create', 'update' => 'zone_edit', 'delete' => 'zone_delete'];
    }

    protected function deletableQuery(): Builder
    {
        return Zone::query();
    }

    public function render()
    {
        return view('livewire.admin.management.zones', [
            'zones' => Zone::query()
                ->with('hub:id,name')
                ->withCount(['tariffs', 'regions'])
                ->when($this->hubId !== '', fn ($q) => $q->where('hub_id', (int) $this->hubId))
                ->when($this->search !== '', fn ($q) => $q->whereLike('name', "%{$this->search}%"))
                ->orderBy('name')
                ->paginate(10),
            'hubs' => Hub::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
