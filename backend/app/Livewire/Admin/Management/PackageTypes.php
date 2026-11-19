<?php

namespace App\Livewire\Admin\Management;

use App\Livewire\Admin\Concerns\ConfirmsDeletion;
use App\Livewire\Admin\Concerns\ManagesFormModal;
use App\Livewire\Admin\Concerns\Toasts;
use App\Livewire\Admin\Management\Forms\PackageTypeForm;
use App\Models\PackageType;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Title;
use Livewire\Component;

/** Paket Növləri (spec §7.4). */
#[Title('Paket Növləri')]
class PackageTypes extends Component
{
    use ConfirmsDeletion, ManagesFormModal, Toasts;

    public PackageTypeForm $form;

    protected function deletableQuery(): Builder
    {
        return PackageType::query();
    }

    public function render()
    {
        return view('livewire.admin.management.package-types', [
            'types' => PackageType::query()->orderBy('min_weight')->get(),
        ]);
    }
}
