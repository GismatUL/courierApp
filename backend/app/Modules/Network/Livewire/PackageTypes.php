<?php

namespace App\Modules\Network\Livewire;

use App\Modules\Network\Livewire\Forms\PackageTypeForm;
use App\Modules\Network\Models\PackageType;
use App\Shared\Livewire\Concerns\ConfirmsDeletion;
use App\Shared\Livewire\Concerns\HasAbilities;
use App\Shared\Livewire\Concerns\ManagesFormModal;
use App\Shared\Livewire\Concerns\Toasts;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Title;
use Livewire\Component;

/** Paket Növləri (spec §7.4). */
#[Title('Paket Növləri')]
class PackageTypes extends Component
{
    use ConfirmsDeletion, HasAbilities, ManagesFormModal, Toasts;

    public PackageTypeForm $form;

    protected function abilities(): array
    {
        return ['create' => 'zone_create', 'update' => 'zone_edit', 'delete' => 'zone_delete'];
    }

    protected function deletableQuery(): Builder
    {
        return PackageType::query();
    }

    public function render()
    {
        return view('network::package-types', [
            'types' => PackageType::query()->orderBy('min_weight')->get(),
        ]);
    }
}
