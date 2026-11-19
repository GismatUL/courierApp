<?php

namespace App\Modules\Parcels\Livewire;

use App\Modules\Merchants\Models\Merchant;
use App\Modules\Network\Enums\TariffType;
use App\Modules\Network\Models\PackageType;
use App\Modules\Network\Models\Zone;
use App\Modules\Parcels\Services\ParcelService;
use App\Shared\Support\Spreadsheet;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Excel import (spec §4.7): a merchant and an .xlsx file whose columns follow the create form (§4.2).
 * Every row is validated like a manual parcel; if any row fails, nothing is imported and the errors are
 * listed per row (no half-imported files). The sample template is generated, not a static file (§13.3 bug).
 */
#[Title('Paketləri Excel-dən import et')]
class ImportParcels extends Component
{
    use WithFileUploads;

    public const COLUMNS = [
        'customer_name' => 'Müştəri adı *', 'customer_phone' => 'Telefon', 'customer_address' => 'Ünvan *',
        'latitude' => 'Enlik *', 'longitude' => 'Uzunluq *', 'zone' => 'Zona *', 'package_type' => 'Paket növü *',
        'tariff' => 'Tarif *', 'parcel_count' => 'Paket sayı *', 'weight' => 'Çəki *', 'cash_collection' => 'Nağd toplama',
        'merchant_invoice_no' => 'Qaimə nömrəsi', 'note' => 'Qeyd',
    ];

    public string $merchantId = '';

    public ?UploadedFile $file = null;

    /** @var array<int, string> row number => message */
    public array $rowErrors = [];

    public ?int $imported = null;

    public function import(ParcelService $service): void
    {
        Gate::authorize('parcel_create');
        $this->validate([
            'merchantId' => ['required', 'integer', 'exists:merchants,id'],
            'file' => ['required', 'file', 'mimes:xlsx', 'max:10240'],
        ], [], ['merchantId' => 'Partnyor', 'file' => 'Fayl']);

        $this->rowErrors = [];
        $this->imported = null;
        $rows = [];
        $zones = Zone::pluck('id', 'name')->mapWithKeys(fn ($id, $name) => [mb_strtolower($name) => $id]);
        $types = PackageType::pluck('id', 'name')->mapWithKeys(fn ($id, $name) => [mb_strtolower($name) => $id]);
        $tariffs = collect(TariffType::cases())->flatMap(fn ($t) => [mb_strtolower($t->label()) => $t->value, $t->value => $t->value]);
        $labels = array_flip(array_map('mb_strtolower', self::COLUMNS));

        foreach (Spreadsheet::read($this->file->getRealPath()) as $number => $raw) {
            // Accept either the column keys or the Azerbaijani headers of the template.
            $row = [];
            foreach ($raw as $header => $value) {
                $row[$labels[$header] ?? $header] = $value;
            }

            $input = [
                'merchant_id' => (int) $this->merchantId,
                'customer_name' => $row['customer_name'] ?? '', 'customer_phone' => ($row['customer_phone'] ?? '') ?: null,
                'customer_address' => $row['customer_address'] ?? '', 'latitude' => $row['latitude'] ?? '', 'longitude' => $row['longitude'] ?? '',
                'zone_id' => $zones[mb_strtolower($row['zone'] ?? '')] ?? null,
                'package_type_id' => $types[mb_strtolower($row['package_type'] ?? '')] ?? null,
                'tariff_type' => $tariffs[mb_strtolower($row['tariff'] ?? '')] ?? null,
                'parcel_count' => ($row['parcel_count'] ?? '') ?: 1, 'weight' => $row['weight'] ?? '',
                'cash_collection' => ($row['cash_collection'] ?? '') ?: 0, 'merchant_invoice_no' => ($row['merchant_invoice_no'] ?? '') ?: null,
                'note' => ($row['note'] ?? '') ?: null, 'created_date' => now()->toDateTimeString(), 'priority' => 'normal',
                'fragile_liquid' => false, 'has_round_trip' => false,
            ];

            try {
                $rows[] = $service->validate($input);
            } catch (ValidationException $e) {
                $this->rowErrors[$number] = collect($e->errors())->flatten()->implode(' ');
            }
        }

        if ($rows === [] && $this->rowErrors === []) {
            $this->addError('file', 'Faylda məlumat sətri yoxdur.');

            return;
        }

        if ($this->rowErrors !== []) {
            return;
        }

        DB::transaction(fn () => array_map(fn ($data) => $service->save(null, $data, auth()->id()), $rows));
        $this->imported = count($rows);
        $this->reset('file');
    }

    public function render()
    {
        return view('parcels::import', [
            'merchants' => Merchant::query()->where('status', true)->orderBy('business_name')->get(['id', 'business_name']),
        ]);
    }
}
