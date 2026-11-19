<?php

namespace App\Modules\Network\Livewire;

use App\Modules\Network\Livewire\Forms\RejectReasonForm;
use App\Modules\Network\Models\RejectReason;
use App\Shared\Livewire\Concerns\ConfirmsDeletion;
use App\Shared\Livewire\Concerns\HasAbilities;
use App\Shared\Livewire\Concerns\ManagesFormModal;
use App\Shared\Livewire\Concerns\Toasts;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Title;
use Livewire\Component;

/** Geri Qaytarma səbəbləri (spec §7.7). */
#[Title('Geri Qaytarma səbəbləri')]
class RejectReasons extends Component
{
    use ConfirmsDeletion, HasAbilities, ManagesFormModal, Toasts;

    public RejectReasonForm $form;

    protected function abilities(): array
    {
        return ['create' => 'zone_create', 'update' => 'zone_edit', 'delete' => 'zone_delete'];
    }

    protected function deletableQuery(): Builder
    {
        return RejectReason::query();
    }

    public function render()
    {
        return view('network::reject-reasons', [
            'reasons' => RejectReason::query()->orderBy('id')->get(),
        ]);
    }
}
