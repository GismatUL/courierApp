<?php

namespace App\Modules\Settings\Livewire;

use App\Modules\Settings\Livewire\Forms\CurrencyForm;
use App\Modules\Settings\Models\Currency;
use App\Modules\Settings\Services\Settings;
use App\Shared\Livewire\Concerns\ConfirmsDeletion;
use App\Shared\Livewire\Concerns\HasAbilities;
use App\Shared\Livewire\Concerns\ManagesFormModal;
use App\Shared\Livewire\Concerns\Toasts;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Title;
use Livewire\Component;

/** Valyuta (spec §10.4). */
#[Title('Valyuta')]
class Currencies extends Component
{
    use ConfirmsDeletion, HasAbilities, ManagesFormModal, Toasts;

    public CurrencyForm $form;

    protected function abilities(): array
    {
        return ['create' => 'currency_create', 'update' => 'currency_update', 'delete' => 'currency_delete'];
    }

    protected function deletableQuery(): Builder
    {
        return Currency::query();
    }

    public function render(Settings $settings)
    {
        return view('settings::currencies', [
            'currencies' => Currency::query()->orderBy('code')->get(),
            'default' => $settings->get('currency'),
        ]);
    }
}
