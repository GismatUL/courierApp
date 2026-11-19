<?php

namespace App\Livewire\Admin\Management;

use App\Enums\TariffType;
use App\Livewire\Admin\Concerns\Toasts;
use App\Models\PackageType;
use App\Models\Zone;
use App\Services\ZoneTariffService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Zone edit = tariff matrix (spec §7.3): one row per package type × tariff type with
 * tracking time, delivery time and price. A row with an empty price is not offered.
 */
class ZoneTariffs extends Component
{
    use Toasts;

    public Zone $zone;

    /** @var array<int, array<string, array{tracking_time: string, delivery_time: string, price: string}>> */
    public array $rows = [];

    public function mount(Zone $zone): void
    {
        $this->zone = $zone;
        $existing = $zone->tariffs()->get()->keyBy(fn ($t) => $t->package_type_id.'.'.$t->tariff_type->value);

        foreach ($this->packageTypes as $type) {
            foreach (TariffType::cases() as $tariff) {
                $cell = $existing->get($type->id.'.'.$tariff->value);
                $this->rows[$type->id][$tariff->value] = [
                    'tracking_time' => (string) $cell?->tracking_time,
                    'delivery_time' => (string) $cell?->delivery_time,
                    'price' => $cell ? (string) (float) $cell->price : '',
                ];
            }
        }
    }

    /** Active types plus any inactive type that still has prices in this zone. */
    #[Computed]
    public function packageTypes(): Collection
    {
        return PackageType::query()
            ->where('status', true)
            ->orWhereHas('zoneTariffs', fn ($q) => $q->where('zone_id', $this->zone->id))
            ->orderBy('min_weight')
            ->get();
    }

    protected function rules(): array
    {
        return [
            'rows' => ['array'],
            'rows.*.*.tracking_time' => ['nullable', 'string', 'max:50'],
            'rows.*.*.delivery_time' => ['nullable', 'string', 'max:50'],
            'rows.*.*.price' => ['nullable', 'numeric', 'min:0', 'max:9999999', 'decimal:0,2',
                'required_with:rows.*.*.tracking_time,rows.*.*.delivery_time'],
        ];
    }

    protected function messages(): array
    {
        return ['rows.*.*.price.required_with' => 'Müddət yazılıbsa, qiymət də daxil edilməlidir.'];
    }

    protected function validationAttributes(): array
    {
        return ['rows.*.*.price' => 'Qiymət', 'rows.*.*.tracking_time' => 'Götürülmə müddəti', 'rows.*.*.delivery_time' => 'Çatdırılma müddəti'];
    }

    public function save(ZoneTariffService $service): void
    {
        $this->validate();
        $allowed = $this->packageTypes->pluck('id')->all();
        $count = $service->sync($this->zone, array_intersect_key($this->rows, array_flip($allowed)));

        $this->toast("Tarif cədvəli yadda saxlanıldı ({$count} qiymət).");
    }

    public function render()
    {
        return view('livewire.admin.management.zone-tariffs', ['tariffs' => TariffType::cases()])
            ->title("{$this->zone->name} — tarif cədvəli");
    }
}
