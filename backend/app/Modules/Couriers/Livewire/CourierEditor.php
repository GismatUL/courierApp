<?php

namespace App\Modules\Couriers\Livewire;

use App\Modules\Couriers\Enums\BonusType;
use App\Modules\Couriers\Enums\CarType;
use App\Modules\Couriers\Enums\CourierType;
use App\Modules\Couriers\Models\Courier;
use App\Modules\Couriers\Services\CourierService;
use App\Modules\Network\Models\Hub;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

/** Kuryer create/edit (spec §7.2 POST /admin/deliveryman/store). */
class CourierEditor extends Component
{
    use WithFileUploads;

    #[Locked]
    public ?int $courierId = null;

    /** @var array<string, string> Login account (name, email, mobile, password, status). */
    public array $account = ['name' => '', 'email' => '', 'mobile' => '', 'password' => '', 'status' => '1'];

    /** @var array<string, string> */
    public array $profile = [];

    /** @var array<string, ?UploadedFile> */
    public array $files = ['image_file' => null, 'driving_license_file' => null];

    private const PROFILE = [
        'hub_id' => '', 'type' => 'company', 'car_type' => 'car', 'car_number' => '', 'voen' => '',
        'min_delivery_parcel' => '0', 'min_pickup_parcel' => '0', 'delivery_charge' => '0', 'pickup_charge' => '0',
        'return_charge' => '0', 'bonus_type' => '', 'salary' => '0', 'work_days' => '', 'opening_balance' => '0',
        'address' => '', 'latitude' => '', 'longitude' => '',
    ];

    public function mount(?Courier $courier = null): void
    {
        Gate::authorize($courier?->exists ? 'delivery_man_update' : 'delivery_man_create');
        $this->profile = self::PROFILE;

        if ($courier?->exists) {
            $this->courierId = $courier->id;
            $user = $courier->user;
            $this->account = ['name' => $user->name, 'email' => $user->email, 'mobile' => (string) $user->mobile, 'password' => '', 'status' => $user->status ? '1' : '0'];

            foreach (array_keys(self::PROFILE) as $key) {
                $value = $courier->{$key};
                $this->profile[$key] = match (true) {
                    $value instanceof \BackedEnum => $value->value,
                    is_numeric($value) && str_contains((string) $value, '.') => (string) (float) $value,
                    default => (string) $value,
                };
            }
        }
    }

    protected function rules(): array
    {
        $money = ['required', 'numeric', 'min:0', 'max:9999999'];
        $userId = $this->courierId ? Courier::find($this->courierId)?->user_id : null;

        return [
            'account.name' => ['required', 'string', 'max:255'],
            'account.email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'account.mobile' => ['required', 'string', 'regex:/^\+?994\d{9}$/'],
            'account.password' => [$this->courierId ? 'nullable' : 'required', 'string', 'min:10', 'max:72'],
            'account.status' => ['required', 'boolean'],
            'profile.hub_id' => ['required', 'integer', 'exists:hubs,id'],
            'profile.type' => ['required', Rule::enum(CourierType::class)],
            'profile.car_type' => ['required', Rule::enum(CarType::class)],
            'profile.car_number' => ['nullable', 'string', 'max:20'],
            'profile.voen' => ['nullable', 'string', 'regex:/^\d{10}$/'],
            'profile.min_delivery_parcel' => ['required', 'integer', 'min:0', 'max:100000'],
            'profile.min_pickup_parcel' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'profile.delivery_charge' => $money,
            'profile.pickup_charge' => $money,
            'profile.return_charge' => $money,
            'profile.bonus_type' => ['nullable', Rule::enum(BonusType::class)],
            'profile.salary' => $money,
            'profile.work_days' => ['nullable', 'integer', 'min:0', 'max:31'],
            'profile.opening_balance' => [$this->courierId ? 'nullable' : 'required', 'numeric', 'min:0', 'max:9999999'],
            'profile.address' => ['nullable', 'string', 'max:500'],
            'profile.latitude' => ['nullable', 'required_with:profile.address', 'numeric', 'between:-90,90'],
            'profile.longitude' => ['nullable', 'required_with:profile.address', 'numeric', 'between:-180,180'],
            'files.image_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'files.driving_license_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    protected function messages(): array
    {
        return [
            'account.mobile.regex' => 'Telefon 994XXXXXXXXX formatında olmalıdır.',
            'profile.voen.regex' => 'VÖEN 10 rəqəmdən ibarət olmalıdır.',
            'profile.latitude.required_with' => 'Ünvan yazılıbsa, koordinatlar da lazımdır.',
            'profile.longitude.required_with' => 'Ünvan yazılıbsa, koordinatlar da lazımdır.',
            'files.*.mimes' => 'Yalnız :values faylı yükləmək olar.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'account.name' => 'Ad', 'account.email' => 'Email', 'account.mobile' => 'Telefon', 'account.password' => 'Şifrə',
            'profile.hub_id' => 'Mərkəz', 'profile.type' => 'Kuryer növü', 'profile.car_type' => 'Nəqliyyat növü',
            'profile.min_delivery_parcel' => 'Minimum çatdırılma paketi', 'profile.min_pickup_parcel' => 'Minimum yığma paketi',
            'profile.delivery_charge' => 'Çatdırılma haqqı', 'profile.pickup_charge' => 'Yığma haqqı', 'profile.return_charge' => 'Geri qaytarma haqqı',
            'profile.salary' => 'Maaş', 'profile.work_days' => 'İş günü', 'profile.opening_balance' => 'Açılış balansı',
            'profile.latitude' => 'Enlik', 'profile.longitude' => 'Uzunluq',
            'files.image_file' => 'Şəkil', 'files.driving_license_file' => 'Sürücülük vəsiqəsi',
        ];
    }

    public function save(CourierService $service)
    {
        $courier = $this->courierId ? Courier::with('user')->findOrFail($this->courierId) : null;
        Gate::authorize($courier ? 'delivery_man_update' : 'delivery_man_create');
        $this->validate();

        $blank = fn (array $values) => array_map(fn ($v) => $v === '' ? null : $v, $values);
        $account = $blank($this->account);
        $account['password'] ??= '';
        $account['status'] = (bool) $account['status'];
        $profile = $blank($this->profile);
        $profile['min_pickup_parcel'] ??= 0;

        $service->save($courier, $account, $profile, $this->files);
        session()->flash('toast', $courier ? 'Kuryer yeniləndi.' : 'Kuryer əlavə edildi.');

        return $this->redirectRoute('admin.couriers.index', navigate: true);
    }

    public function render()
    {
        $courier = $this->courierId ? Courier::find($this->courierId) : null;

        return view('couriers::courier-editor', [
            'courier' => $courier,
            'hubs' => Hub::query()->where('status', true)->orWhere('id', (int) ($this->profile['hub_id'] ?? 0))->orderBy('name')->get(['id', 'name']),
            'types' => CourierType::cases(),
            'carTypes' => CarType::cases(),
            'bonusTypes' => BonusType::cases(),
        ])->title($courier ? "Kuryer: {$courier->user->name}" : 'Yeni kuryer');
    }
}
