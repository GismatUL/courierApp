<?php

namespace App\Modules\Parcels\Services;

use App\Modules\Merchants\Models\Merchant;
use App\Modules\Merchants\Models\MerchantShop;
use App\Modules\Network\Models\PackageType;
use App\Modules\Parcels\Enums\ParcelStatus;
use App\Modules\Parcels\Models\Customer;
use App\Modules\Parcels\Models\Parcel;
use App\Modules\Parcels\Models\ParcelEvent;
use App\Modules\Settings\Services\Settings;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator as ValidatorContract;
use Illuminate\Support\Facades\Validator;

/**
 * Parcel create / edit / delete (spec §4.2) shared by the admin form, Excel import and the API.
 * - validates the spec fields, and that the weight fits the package type and the shop belongs to the merchant;
 * - prices from Pricing and refuses a parcel without a tariff (spec §13.3: no more 0.00 parcels);
 * - assigns the tracking code (prefix from settings + digits + "C" + digits), the sequential invoice number,
 *   the creator, status "Gözləmədə", and adds the recipient to Customers (spec §9.5).
 */
class ParcelService
{
    public function __construct(private readonly Pricing $pricing, private readonly Settings $settings) {}

    /** @return array<string, list<mixed>> */
    public static function rules(): array
    {
        return [
            'merchant_id' => ['required', 'integer', Rule::exists('merchants', 'id')->where('status', true)],
            'merchant_shop_id' => ['nullable', 'integer'],
            'pickupman_id' => ['nullable', 'integer', 'exists:delivery_men,id'],
            'merchant_invoice_no' => ['nullable', 'string', 'max:50'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'regex:/^\+?[0-9 ()-]{7,20}$/'],
            'customer_address' => ['required', 'string', 'max:500'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'zone_id' => ['required', 'integer', Rule::exists('zones', 'id')->where('status', true)],
            'package_type_id' => ['required', 'integer', Rule::exists('package_types', 'id')->where('status', true)],
            'tariff_type' => ['required', 'in:urgent,same_day,regular,express'],
            'parcel_count' => ['required', 'integer', 'min:1', 'max:1000'],
            'weight' => ['required', 'numeric', 'gt:0', 'max:10000'],
            'created_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:2000'],
            'pickup_address' => ['nullable', 'string', 'max:500'],
            'pickup_point' => ['nullable', 'string', 'max:1000'],
            'fragile_liquid' => ['boolean'],
            'has_round_trip' => ['boolean'],
            'cash_collection' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'priority' => ['required', 'in:normal,high'],
        ];
    }

    /** @return array<string, string> */
    public static function attributes(): array
    {
        return [
            'merchant_id' => 'Partnyor', 'merchant_shop_id' => 'Filial', 'customer_name' => 'Müştəri adı', 'customer_phone' => 'Telefon',
            'customer_address' => 'Ünvan', 'latitude' => 'Enlik', 'longitude' => 'Uzunluq', 'zone_id' => 'Zona', 'package_type_id' => 'Paket növü',
            'tariff_type' => 'Tarif', 'parcel_count' => 'Paket sayı', 'weight' => 'Çəki', 'created_date' => 'Tarix', 'cash_collection' => 'Nağd toplama',
        ];
    }

    /**
     * Validates input (keys as in rules()) and returns the clean values; throws ValidationException.
     *
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function validate(array $input, string $prefix = ''): array
    {
        $validator = Validator::make($input, self::rules(), [], self::attributes());
        $validator->after(fn (ValidatorContract $v) => $this->checkConsistency($v, $input));

        try {
            $data = $validator->validate();
        } catch (ValidationException $e) {
            throw $prefix === '' ? $e : ValidationException::withMessages(
                collect($e->errors())->mapWithKeys(fn ($messages, $key) => ["{$prefix}{$key}" => $messages])->all()
            );
        }

        return $data;
    }

    private function checkConsistency(ValidatorContract $v, array $input): void
    {
        if ($v->errors()->isNotEmpty()) {
            return;
        }

        if (! empty($input['merchant_shop_id']) && ! MerchantShop::where('merchant_id', $input['merchant_id'])->whereKey($input['merchant_shop_id'])->exists()) {
            $v->errors()->add('merchant_shop_id', 'Bu filial seçilmiş partnyora aid deyil.');
        }

        // Spec §7.4: the weight must fit the chosen package type (it was not checked in the old system).
        $type = PackageType::find($input['package_type_id']);
        $weight = (float) $input['weight'];
        if ($type && ($weight < (float) $type->min_weight || ($type->max_weight !== null && $weight > (float) $type->max_weight))) {
            $v->errors()->add('weight', "Çəki \"{$type->name}\" növünün aralığına ({$type->weightRange()}) uyğun deyil.");
        }

        if (! $this->pricing->quote((int) $input['merchant_id'], (int) $input['zone_id'], (int) $input['package_type_id'], $input['tariff_type'])) {
            $v->errors()->add('tariff_type', 'Bu partnyor, zona, paket növü və tarif üçün qiymət təyin edilməyib.');
        }
    }

    /** @param array<string, mixed> $data validated values */
    public function save(?Parcel $parcel, array $data, ?int $actorId): Parcel
    {
        return DB::transaction(function () use ($parcel, $data, $actorId) {
            $quote = $this->pricing->quote((int) $data['merchant_id'], (int) $data['zone_id'], (int) $data['package_type_id'], $data['tariff_type'], (bool) ($data['has_round_trip'] ?? false));
            $data['cash_collection'] = $data['cash_collection'] ?? 0;

            if ($parcel === null) {
                $parcel = new Parcel;
                $parcel->tracking_id = $this->newTrackingId();
                $parcel->invoice_no = (int) DB::selectOne("SELECT nextval('parcel_invoice_no_seq') AS n")->n;
                $parcel->created_by = $actorId;
                $parcel->status = ParcelStatus::Pending;
                $parcel->status_updated_at = now();
                $parcel->map_status = 2;
            } elseif ($parcel->status !== ParcelStatus::Pending) {
                throw ValidationException::withMessages(['merchant_id' => 'Yalnız "Gözləmədə" statusundakı paket redaktə edilə bilər.']);
            }

            if (empty($data['pickup_address'])) {
                $data['pickup_address'] = MerchantShop::find($data['merchant_shop_id'] ?? 0)?->address ?? Merchant::find($data['merchant_id'])?->address;
            }

            $parcel->fill($data);
            $parcel->merchant_shop_id = $data['merchant_shop_id'] ?? null;
            $parcel->delivery_charge = $quote['price'];
            $parcel->total = Pricing::total($parcel->cash_collection, $quote['price'], (int) $parcel->parcel_count);
            $parcel->customer_id = Customer::firstOrCreate(
                ['phone' => $data['customer_phone'] ?? null, 'name' => $data['customer_name']],
                ['address' => $data['customer_address']],
            )->id;
            $isNew = ! $parcel->exists;
            $parcel->save();

            if ($isNew) {
                ParcelEvent::create([
                    'parcel_id' => $parcel->id, 'action' => 'create', 'status' => ParcelStatus::Pending,
                    'actor_user_id' => $actorId, 'note' => $data['note'] ?? null,
                ]);
            }

            return $parcel->refresh();
        });
    }

    public function delete(Parcel $parcel): void
    {
        $parcel->delete();
    }

    private function newTrackingId(): string
    {
        $prefix = strtoupper((string) $this->settings->get('par_track_prefix', 'KN'));

        do {
            $id = $prefix.str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT).'C'.str_pad((string) random_int(0, 9999999), 7, '0', STR_PAD_LEFT);
        } while (Parcel::where('tracking_id', $id)->exists());

        return $id;
    }
}
