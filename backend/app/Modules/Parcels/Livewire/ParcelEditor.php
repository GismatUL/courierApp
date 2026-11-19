<?php

namespace App\Modules\Parcels\Livewire;

use App\Modules\Couriers\Models\Courier;
use App\Modules\Merchants\Models\Merchant;
use App\Modules\Merchants\Models\MerchantShop;
use App\Modules\Network\Enums\TariffType;
use App\Modules\Network\Models\PackageType;
use App\Modules\Network\Models\Zone;
use App\Modules\Parcels\Models\Parcel;
use App\Modules\Parcels\Services\ParcelService;
use App\Modules\Parcels\Services\Pricing;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Paket yaratma / redaktə (spec §4.2). The right panel shows the live price ("Qiymət Detalları"):
 * it is re-quoted whenever merchant, zone, package type, tariff, count, COD or round trip change.
 * Only parcels still "Gözləmədə" can be edited.
 */
class ParcelEditor extends Component
{
    #[Locked]
    public ?int $parcelId = null;

    /** @var array<string, mixed> */
    public array $data = [];

    public function mount(?Parcel $parcel = null): void
    {
        Gate::authorize($parcel?->exists ? 'parcel_update' : 'parcel_create');

        $this->data = [
            'merchant_id' => '', 'merchant_shop_id' => '', 'pickupman_id' => '', 'merchant_invoice_no' => '',
            'customer_name' => '', 'customer_phone' => '', 'customer_address' => '', 'latitude' => '', 'longitude' => '',
            'zone_id' => '', 'package_type_id' => '', 'tariff_type' => 'regular', 'parcel_count' => '1', 'weight' => '',
            'created_date' => now('Asia/Baku')->format('Y-m-d\TH:i'), 'note' => '', 'pickup_address' => '', 'pickup_point' => '',
            'fragile_liquid' => false, 'has_round_trip' => false, 'cash_collection' => '0', 'priority' => 'normal',
        ];

        if ($parcel?->exists) {
            abort_unless($parcel->status->value === 1, 403, 'Yalnız "Gözləmədə" statusundakı paket redaktə edilə bilər.');
            $this->parcelId = $parcel->id;

            foreach (array_keys($this->data) as $key) {
                $value = $parcel->{$key};
                $this->data[$key] = match (true) {
                    $value instanceof \BackedEnum => $value->value,
                    $value instanceof \DateTimeInterface => $value->timezone('Asia/Baku')->format('Y-m-d\TH:i'),
                    is_bool($value) => $value,
                    is_numeric($value) && str_contains((string) $value, '.') => (string) (float) $value,
                    default => (string) $value,
                };
            }
        }
    }

    public function updatedDataMerchantId(): void
    {
        $this->data['merchant_shop_id'] = ''; // spec §4.2: changing the merchant resets the shop
        $this->data['merchant_shop_id'] = (string) (MerchantShop::where('merchant_id', (int) $this->data['merchant_id'])->where('is_default', true)->value('id') ?? '');
    }

    public function save(ParcelService $service)
    {
        $parcel = $this->parcelId ? Parcel::findOrFail($this->parcelId) : null;
        Gate::authorize($parcel ? 'parcel_update' : 'parcel_create');

        $input = array_map(fn ($v) => $v === '' ? null : $v, $this->data);
        $input['created_date'] ??= now()->toDateTimeString();
        $saved = $service->save($parcel, $service->validate($input, 'data.'), auth()->id());

        session()->flash('toast', $parcel ? 'Paket yeniləndi.' : "Paket yaradıldı: {$saved->tracking_id}");

        return $this->redirectRoute('admin.parcels.show', $saved, navigate: true);
    }

    public function render(Pricing $pricing)
    {
        $d = $this->data;
        $quote = ($d['merchant_id'] && $d['zone_id'] && $d['package_type_id'] && $d['tariff_type'])
            ? $pricing->quote((int) $d['merchant_id'], (int) $d['zone_id'], (int) $d['package_type_id'], $d['tariff_type'], (bool) $d['has_round_trip'])
            : null;
        $count = max(1, (int) $d['parcel_count']);

        return view('parcels::parcel-editor', [
            'quote' => $quote,
            'total' => $quote ? Pricing::total((float) $d['cash_collection'], $quote['price'], $count) : null,
            'merchants' => Merchant::query()->where('status', true)->orderBy('business_name')->get(['id', 'business_name']),
            'shops' => $d['merchant_id'] ? MerchantShop::where('merchant_id', (int) $d['merchant_id'])->where('status', true)->orderBy('name')->get(['id', 'name']) : collect(),
            'zones' => Zone::query()->where('status', true)->orderBy('name')->get(['id', 'name']),
            'packageTypes' => PackageType::query()->where('status', true)->orderBy('min_weight')->get(),
            'tariffs' => TariffType::cases(),
            'couriers' => Courier::query()->with('user:id,name')->whereHas('user', fn ($q) => $q->where('status', true))->get()->sortBy('user.name'),
        ])->title($this->parcelId ? 'Paketi redaktə et' : 'Yeni paket');
    }
}
