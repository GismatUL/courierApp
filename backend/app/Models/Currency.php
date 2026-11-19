<?php

namespace App\Models;

use App\Services\Settings;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/** Spec §10.4. Only AZN is used in practice; the exchange rate is "1 USD = ?". */
#[Fillable(['name', 'code', 'symbol', 'exchange_rate', 'position', 'status'])]
class Currency extends Model
{
    protected $attributes = ['status' => true, 'position' => 'after'];

    protected function casts(): array
    {
        return ['exchange_rate' => 'decimal:6', 'status' => 'boolean'];
    }

    public function format(float|string $amount): string
    {
        $value = number_format((float) $amount, 2, '.', ' ');

        return $this->position === 'before' ? $this->symbol.$value : $value.' '.$this->symbol;
    }

    public function deletionBlocker(): ?string
    {
        return app(Settings::class)->get('currency') === $this->code ? 'Bu, sistemin əsas valyutasıdır.' : null;
    }
}
