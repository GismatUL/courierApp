<?php

namespace App\Livewire\Admin\Management;

use App\Livewire\Admin\Concerns\ConfirmsDeletion;
use App\Livewire\Admin\Concerns\HasAbilities;
use App\Livewire\Admin\Concerns\ManagesFormModal;
use App\Livewire\Admin\Concerns\Toasts;
use App\Livewire\Admin\Management\Forms\ShelfForm;
use App\Models\Shelf;
use App\Models\Zone;
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
        return view('livewire.admin.management.shelves', [
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
