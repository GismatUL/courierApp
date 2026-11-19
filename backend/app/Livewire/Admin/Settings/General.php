<?php

namespace App\Livewire\Admin\Settings;

use App\Livewire\Admin\Concerns\Toasts;
use App\Models\Currency;
use App\Services\Settings;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

/** Ümumi Tənzimləmələr (spec §10.2). Branding files go to the public "uploads" disk under generated names. */
#[Title('Ümumi Tənzimləmələr')]
class General extends Component
{
    use Toasts, WithFileUploads;

    private const TEXT_KEYS = ['name', 'phone', 'email', 'address', 'tax', 'currency', 'par_track_prefix', 'invoice_prefix', 'copyright', 'primary_color', 'text_color'];

    private const FILE_KEYS = ['logo', 'light_logo', 'favicon'];

    /** @var array<string, ?string> */
    public array $values = [];

    /** @var array<string, ?UploadedFile> */
    public array $files = ['logo' => null, 'light_logo' => null, 'favicon' => null];

    public function mount(Settings $settings): void
    {
        $all = $settings->all();
        $this->values = array_map(fn ($key) => (string) ($all[$key] ?? ''), array_combine(self::TEXT_KEYS, self::TEXT_KEYS));
    }

    protected function rules(): array
    {
        return [
            'values.name' => ['required', 'string', 'max:100'],
            'values.phone' => ['nullable', 'string', 'max:30'],
            'values.email' => ['nullable', 'email', 'max:255'],
            'values.address' => ['nullable', 'string', 'max:500'],
            'values.tax' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'values.currency' => ['required', 'string', 'exists:currencies,code'],
            'values.par_track_prefix' => ['required', 'string', 'regex:/^[A-Z]{1,5}$/'],
            'values.invoice_prefix' => ['nullable', 'string', 'regex:/^[A-Z0-9-]{1,10}$/'],
            'values.copyright' => ['nullable', 'string', 'max:255'],
            'values.primary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'values.text_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            // No SVG: it can carry scripts and these files are served publicly.
            'files.logo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'files.light_logo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'files.favicon' => ['nullable', 'file', 'mimes:png,ico', 'max:512'],
        ];
    }

    protected function messages(): array
    {
        return [
            'values.par_track_prefix.regex' => 'Prefiks 1–5 böyük latın hərfi olmalıdır (məs. KN).',
            'values.invoice_prefix.regex' => 'Prefiks böyük hərf, rəqəm və "-" ola bilər (10 simvola qədər).',
            'values.primary_color.regex' => 'Rəng #RRGGBB formatında olmalıdır.',
            'values.text_color.regex' => 'Rəng #RRGGBB formatında olmalıdır.',
            'files.*.mimes' => 'Yalnız :values faylı yükləmək olar.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'values.name' => 'Tətbiq adı', 'values.phone' => 'Telefon', 'values.email' => 'Email', 'values.address' => 'Ünvan',
            'values.tax' => 'Vergi', 'values.currency' => 'Valyuta', 'values.par_track_prefix' => 'İzləmə kodu prefiksi',
            'values.invoice_prefix' => 'Faktura prefiksi', 'values.copyright' => 'Copyright',
            'files.logo' => 'Loqo', 'files.light_logo' => 'Açıq loqo', 'files.favicon' => 'Favicon',
        ];
    }

    public function updatedValues(mixed $value, string $key): void
    {
        if (in_array($key, ['par_track_prefix', 'invoice_prefix'], true)) {
            $this->values[$key] = strtoupper(trim((string) $value));
        }
    }

    public function save(Settings $settings): void
    {
        Gate::authorize('general_settings_update');
        $this->validate();

        $data = array_map(fn ($value) => $value === '' ? null : $value, $this->values);

        foreach (self::FILE_KEYS as $key) {
            if ($this->files[$key] instanceof UploadedFile) {
                $data[$key] = $this->storeFile($this->files[$key], $settings->get($key));
            }
        }

        $settings->set($data);
        $this->files = ['logo' => null, 'light_logo' => null, 'favicon' => null];
        $this->toast('Tənzimləmələr yadda saxlanıldı.');
    }

    public function removeFile(string $key, Settings $settings): void
    {
        Gate::authorize('general_settings_update');
        abort_unless(in_array($key, self::FILE_KEYS, true), 404);

        if ($old = $settings->get($key)) {
            Storage::disk('uploads')->delete($old);
        }

        $settings->set([$key => null]);
        $this->toast('Fayl silindi.');
    }

    private function storeFile(UploadedFile $file, ?string $old): string
    {
        $path = Storage::disk('uploads')->putFileAs('settings', $file, Str::uuid().'.'.$file->extension());

        if ($old) {
            Storage::disk('uploads')->delete($old);
        }

        return $path;
    }

    public function render(Settings $settings)
    {
        return view('livewire.admin.settings.general', [
            'currencies' => Currency::query()->where('status', true)->orderBy('code')->get(['code', 'name', 'symbol']),
            'stored' => array_map(fn ($key) => $settings->get($key), array_combine(self::FILE_KEYS, self::FILE_KEYS)),
        ]);
    }
}
