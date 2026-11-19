<?php

namespace App\Livewire\Admin\Management;

use App\Livewire\Admin\Concerns\ConfirmsDeletion;
use App\Livewire\Admin\Concerns\ManagesFormModal;
use App\Livewire\Admin\Concerns\Toasts;
use App\Livewire\Admin\Management\Forms\RejectReasonForm;
use App\Models\RejectReason;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Title;
use Livewire\Component;

/** Geri Qaytarma səbəbləri (spec §7.7). */
#[Title('Geri Qaytarma səbəbləri')]
class RejectReasons extends Component
{
    use ConfirmsDeletion, ManagesFormModal, Toasts;

    public RejectReasonForm $form;

    protected function deletableQuery(): Builder
    {
        return RejectReason::query();
    }

    public function render()
    {
        return view('livewire.admin.management.reject-reasons', [
            'reasons' => RejectReason::query()->orderBy('id')->get(),
        ]);
    }
}
