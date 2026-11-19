<?php

namespace App\Livewire\Admin\Settings;

use App\Livewire\Admin\Concerns\ConfirmsDeletion;
use App\Livewire\Admin\Concerns\ManagesFormModal;
use App\Livewire\Admin\Concerns\Toasts;
use App\Livewire\Admin\Settings\Forms\CurrencyForm;
use App\Models\Currency;
use App\Services\Settings;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Title;
use Livewire\Component;

/** Valyuta (spec §10.4). */
#[Title('Valyuta')]
class Currencies extends Component
{
    use ConfirmsDeletion, ManagesFormModal, Toasts;

    public CurrencyForm $form;

    protected function deletableQuery(): Builder
    {
        return Currency::query();
    }

    public function render(Settings $settings)
    {
        return view('livewire.admin.settings.currencies', [
            'currencies' => Currency::query()->orderBy('code')->get(),
            'default' => $settings->get('currency'),
        ]);
    }
}
