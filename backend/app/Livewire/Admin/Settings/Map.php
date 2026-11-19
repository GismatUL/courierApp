<?php

namespace App\Livewire\Admin\Settings;

use App\Livewire\Admin\Concerns\Toasts;
use App\Services\Settings;
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
        $this->mapKey = trim($this->mapKey);
        $this->validate();
        $settings->set(['map_key' => $this->mapKey ?: null]);
        $this->toast($this->mapKey ? 'Xəritə açarı yadda saxlanıldı.' : 'Xəritə açarı silindi.');
    }

    public function render()
    {
        return view('livewire.admin.settings.map');
    }
}
