<?php

namespace App\Modules\Merchants\Livewire;

use App\Modules\Merchants\Models\Merchant;
use App\Modules\Merchants\Models\MerchantType;
use App\Modules\Merchants\Services\MerchantService;
use App\Modules\Network\Models\Hub;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Partnyor create/edit (spec §6.1 /admin/merchant/store, /update/{id}). The spec marks only hubs,
 * status and OTP as required in HTML; the business name is also required here because every list
 * and invoice shows it. The opening balance can only be set when the merchant is created.
 */
class MerchantEditor extends Component
{
    use WithFileUploads;

    #[Locked]
    public ?int $merchantId = null;

    /** @var array<string, mixed> */
    public array $data = [];

    /** @var list<string> */
    public array $hubIds = [];

    /** @var array<string, ?UploadedFile> */
    public array $files = ['nid_file' => null, 'trade_license_file' => null, 'image_file' => null];

    private const DEFAULTS = [
        'business_name' => '', 'merchant_type_id' => '', 'contact_name' => '', 'email' => '', 'mobile' => '',
        'password' => '', 'address' => '', 'opening_balance' => '0', 'vat' => '0', 'return_charge_pct' => '0',
        'cod_charge_inside_city' => '1', 'cod_charge_sub_city' => '2', 'cod_charge_outside_city' => '3',
        'payment_period_days' => '', 'wallet_enabled' => '0', 'otp_required' => '0',
        'reference_name' => '', 'reference_phone' => '', 'status' => '1',
    ];

    public function mount(?Merchant $merchant = null): void
    {
        Gate::authorize($merchant?->exists ? 'merchant_update' : 'merchant_create');
        $this->data = self::DEFAULTS;

        if ($merchant?->exists) {
            $this->merchantId = $merchant->id;
            foreach (array_keys(self::DEFAULTS) as $key) {
                $value = $merchant->{$key};
                $this->data[$key] = match (true) {
                    $key === 'password' => '',
                    is_bool($value) => $value ? '1' : '0', // <select> options carry "0"/"1"
                    is_numeric($value) && str_contains((string) $value, '.') => (string) (float) $value,
                    default => (string) $value,
                };
            }
            $this->hubIds = $merchant->hubs()->pluck('hubs.id')->map(fn ($id) => (string) $id)->all();
        }
    }

    protected function rules(): array
    {
        $percent = ['required', 'numeric', 'min:0', 'max:100'];

        return [
            'data.business_name' => ['required', 'string', 'max:255'],
            'data.merchant_type_id' => ['nullable', 'integer', 'exists:merchant_types,id'],
            'data.contact_name' => ['nullable', 'string', 'max:255'],
            'data.email' => ['nullable', 'email', 'max:255', Rule::unique('merchants', 'email')->ignore($this->merchantId)],
            'data.mobile' => ['nullable', 'string', 'regex:/^\+?994\d{9}$/'],
            'data.password' => ['nullable', 'string', 'min:10', 'max:72'],
            'data.address' => ['nullable', 'string', 'max:1000'],
            'data.opening_balance' => [$this->merchantId ? 'nullable' : 'required', 'numeric', 'min:-9999999', 'max:9999999'],
            'data.vat' => $percent,
            'data.return_charge_pct' => $percent,
            'data.cod_charge_inside_city' => $percent,
            'data.cod_charge_sub_city' => $percent,
            'data.cod_charge_outside_city' => $percent,
            'data.payment_period_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'data.wallet_enabled' => ['boolean'],
            'data.otp_required' => ['required', 'boolean'],
            'data.reference_name' => ['nullable', 'string', 'max:255'],
            'data.reference_phone' => ['nullable', 'string', 'regex:/^\+?994\d{9}$/'],
            'data.status' => ['required', 'boolean'],
            'hubIds' => ['required', 'array', 'min:1'],
            'hubIds.*' => ['integer', 'exists:hubs,id'],
            // ID card and contract: PDF or photo; logo: image only. No SVG (it can carry scripts).
            'files.nid_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'files.trade_license_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'files.image_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    protected function messages(): array
    {
        return [
            'data.mobile.regex' => 'Telefon 994XXXXXXXXX formatında olmalıdır.',
            'data.reference_phone.regex' => 'Telefon 994XXXXXXXXX formatında olmalıdır.',
            'hubIds.required' => 'Ən azı bir filial seçin.',
            'files.*.mimes' => 'Yalnız :values faylı yükləmək olar.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'data.business_name' => 'Biznes adı', 'data.contact_name' => 'Ad', 'data.email' => 'Email', 'data.password' => 'Şifrə',
            'data.opening_balance' => 'Açılış balansı', 'data.vat' => 'ƏDV', 'data.return_charge_pct' => 'Geri qaytarma haqqı',
            'data.cod_charge_inside_city' => 'Şəhər daxili', 'data.cod_charge_sub_city' => 'Ətraf', 'data.cod_charge_outside_city' => 'Şəhərkənarı',
            'data.payment_period_days' => 'Ödəmə müddəti', 'data.reference_name' => 'İstinad şəxs', 'hubIds' => 'Filiallar',
            'files.nid_file' => 'Şəxsiyyət vəsiqəsi', 'files.trade_license_file' => 'Müqavilə', 'files.image_file' => 'Şəkil / loqo',
        ];
    }

    public function save(MerchantService $service)
    {
        $merchant = $this->merchantId ? Merchant::findOrFail($this->merchantId) : null;
        Gate::authorize($merchant ? 'merchant_update' : 'merchant_create');
        $this->validate();

        $data = array_map(fn ($value) => $value === '' ? null : $value, $this->data);
        $data['password'] ??= '';

        $merchant = $service->save($merchant, $data, array_map('intval', $this->hubIds), $this->files);

        session()->flash('toast', $this->merchantId ? 'Partnyor yeniləndi.' : 'Partnyor əlavə edildi.');

        return $this->redirectRoute(Gate::allows('merchant_view') ? 'admin.merchants.show' : 'admin.merchants.index', Gate::allows('merchant_view') ? $merchant : [], navigate: true);
    }

    public function render()
    {
        $merchant = $this->merchantId ? Merchant::find($this->merchantId) : null;

        return view('merchants::merchant-editor', [
            'merchant' => $merchant,
            'hubs' => Hub::query()->where('status', true)->orWhereIn('id', $this->hubIds ?: [0])->orderBy('name')->get(['id', 'name']),
            'types' => MerchantType::query()->orderBy('title')->get(['id', 'title']),
        ])->title($merchant ? "Partnyor: {$merchant->business_name}" : 'Yeni partnyor');
    }
}
