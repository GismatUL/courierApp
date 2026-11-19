<?php

namespace App\Modules\Network\Livewire;

use App\Modules\Network\Livewire\Forms\ShelfForm;
use App\Modules\Network\Models\Shelf;
use App\Modules\Network\Models\Zone;
use App\Shared\Livewire\Concerns\ConfirmsDeletion;
use App\Shared\Livewire\Concerns\HasAbilities;
use App\Shared\Livewire\Concerns\ManagesFormModal;
use App\Shared\Livewire\Concerns\Toasts;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/** Qutular / rəflər (spec §7.6). Parcel counts and "Təyin Et" come with the courier panel. */
#[Title('Qutular')]
class Shelves extends Component
{
    use ConfirmsDeletion, HasAbilities, ManagesFormModal, Toasts, WithPagination;

    public ShelfForm $form;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'zone', except: '')]
    public string $zoneId = '';

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'zoneId'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset('search', 'zoneId');
        $this->resetPage();
    }

    protected function abilities(): array
    {
        return ['create' => 'shelf_create', 'update' => 'shelf_edit', 'delete' => 'shelf_delete'];
    }

    protected function deletableQuery(): Builder
    {
        return Shelf::query();
    }

    public function render()
    {
        return view('network::shelves', [
            'shelves' => Shelf::query()
                ->with('zone:id,name')
                ->when($this->zoneId !== '', fn ($q) => $q->where('zone_id', (int) $this->zoneId))
                ->when($this->search !== '', fn ($q) => $q->where(fn ($q) => $q
                    ->whereLike('name', "%{$this->search}%")
                    ->orWhereLike('code', "%{$this->search}%")))
                ->orderBy('code')
                ->paginate(10),
            'zones' => Zone::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
