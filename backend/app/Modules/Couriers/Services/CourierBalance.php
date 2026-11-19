<?php

namespace App\Modules\Couriers\Services;

use App\Modules\Couriers\Enums\BalanceChannel;
use App\Modules\Couriers\Models\Courier;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * The only way to change a courier's balances (other modules call this, never the columns).
 * - credit: money the courier collected on delivery (spec §5.1);
 * - debit: money handed over to a cash desk, "Kuryerdən alındı" (spec §8.2); it may not exceed
 *   what the courier holds in that channel.
 * Rows are locked for the update, so concurrent operations cannot lose a change.
 */
class CourierBalance
{
    public function credit(Courier $courier, BalanceChannel $channel, string|float $amount): Courier
    {
        return $this->apply($courier, $channel, $this->positive($amount));
    }

    public function debit(Courier $courier, BalanceChannel $channel, string|float $amount): Courier
    {
        return $this->apply($courier, $channel, -$this->positive($amount));
    }

    private function apply(Courier $courier, BalanceChannel $channel, float $delta): Courier
    {
        return DB::transaction(function () use ($courier, $channel, $delta) {
            $locked = Courier::query()->lockForUpdate()->findOrFail($courier->id);
            $column = $channel->column();
            $next = round((float) $locked->{$column} + $delta, 2);

            if ($next < 0) {
                throw new DomainException("Kuryerin {$channel->label()} balansında kifayət qədər vəsait yoxdur ({$locked->{$column}} ₼).");
            }

            $locked->forceFill([$column => $next])->save();

            return $locked;
        });
    }

    private function positive(string|float $amount): float
    {
        $value = round((float) $amount, 2);

        if ($value <= 0) {
            throw new DomainException('Məbləğ müsbət olmalıdır.');
        }

        return $value;
    }
}
