<?php

namespace App\Modules\Settings\Livewire;

use App\Modules\Settings\Services\Settings;
use App\Shared\Livewire\Concerns\Toasts;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Google Xəritə Tənzimləmələri (spec §10.3). The key is a browser key used on admin and public
 * map pages, so it must be restricted by HTTP referrer in Google Cloud.
 */
#[Title('Google Xəritə')]
class Map extends Component
{
    use Toasts;

    public string $mapKey = '';

    public function mount(Settings $settings): void
    {
        $this->mapKey = (string) $settings->get('map_key');
    }

    protected function rules(): array
    {
        return ['mapKey' => ['nullable', 'string', 'regex:/^AIza[0-9A-Za-z_-]{35}$/']];
    }

    protected function messages(): array
    {
        return ['mapKey.regex' => 'Bu, Google Maps API açarına oxşamır (AIza… ilə başlayan 39 simvol).'];
    }

    public function save(Settings $settings): void
    {
        Gate::authorize('general_settings_update');
        $this->mapKey = trim($this->mapKey);
        $this->validate();
        $settings->set(['map_key' => $this->mapKey ?: null]);
        $this->toast($this->mapKey ? 'Xəritə açarı yadda saxlanıldı.' : 'Xəritə açarı silindi.');
    }

    public function render()
    {
        return view('settings::map');
    }
}
