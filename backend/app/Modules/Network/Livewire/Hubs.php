<?php

namespace App\Modules\Network\Livewire;

use App\Modules\Access\Models\User;
use App\Modules\Network\Livewire\Forms\HubForm;
use App\Modules\Network\Models\Hub;
use App\Shared\Livewire\Concerns\ConfirmsDeletion;
use App\Shared\Livewire\Concerns\HasAbilities;
use App\Shared\Livewire\Concerns\ManagesFormModal;
use App\Shared\Livewire\Concerns\Toasts;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/** Filiallar (spec §7.1): list filtered by name/phone, 10 per page. */
#[Title('Filiallar')]
class Hubs extends Component
{
    use ConfirmsDeletion, HasAbilities, ManagesFormModal, Toasts, WithPagination;

    public HubForm $form;

    #[Url(except: '')]
    public string $name = '';

    #[Url(except: '')]
    public string $phone = '';

    public function updated(string $property): void
    {
        if (in_array($property, ['name', 'phone'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset('name', 'phone');
        $this->resetPage();
    }

    protected function abilities(): array
    {
        return ['create' => 'hub_create', 'update' => 'hub_update', 'delete' => 'hub_delete'];
    }

    protected function deletableQuery(): Builder
    {
        return Hub::query();
    }

    public function render()
    {
        return view('network::hubs', [
            'hubs' => Hub::query()
                ->with('contactUser:id,name')
                ->when($this->name !== '', fn ($q) => $q->whereLike('name', "%{$this->name}%"))
                ->when($this->phone !== '', fn ($q) => $q->whereLike('phone', "%{$this->phone}%"))
                ->orderBy('name')
                ->paginate(10),
            'users' => User::query()->where('status', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
