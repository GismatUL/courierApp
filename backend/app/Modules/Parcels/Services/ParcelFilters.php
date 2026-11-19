<?php

namespace App\Modules\Parcels\Services;

use App\Modules\Parcels\Models\Parcel;
use Illuminate\Database\Eloquent\Builder;

/**
 * The parcel list filters (spec §4.1), shared by the list page, its Excel export and the API, so all
 * three return the same rows. Keys match the list's query string (q, merchant, shops, courier, by…).
 */
class ParcelFilters
{
    /** @param array<string, mixed> $f */
    public static function apply(array $f, ?Builder $query = null): Builder
    {
        $query ??= Parcel::query();
        $value = fn (string $key) => is_string($f[$key] ?? null) ? trim($f[$key]) : '';
        $term = $value('q');
        $invoice = $value('invoice');
        $shops = array_map('intval', (array) ($f['shops'] ?? []));

        return $query
            ->when($value('dateFrom') !== '', fn ($q) => $q->where('created_date', '>=', $value('dateFrom').' 00:00:00'))
            ->when($value('dateTo') !== '', fn ($q) => $q->where('created_date', '<=', $value('dateTo').' 23:59:59'))
            ->when($value('status') !== '', fn ($q) => $q->where('status', (int) $value('status')))
            ->when($value('merchant') !== '', fn ($q) => $q->where('merchant_id', (int) $value('merchant')))
            ->when($shops !== [], fn ($q) => $q->whereIn('merchant_shop_id', $shops))
            ->when($value('courier') !== '', fn ($q) => $q->where(fn ($q) => $q->where('delivery_man_id', (int) $value('courier'))->orWhere('pickupman_id', (int) $value('courier'))))
            ->when($invoice !== '', fn ($q) => $q->where(fn ($q) => $q->where('invoice_no', ctype_digit($invoice) ? (int) $invoice : 0)->orWhereLike('merchant_invoice_no', "%{$invoice}%")))
            ->when($value('address') !== '', fn ($q) => $q->whereLike('customer_address', '%'.$value('address').'%'))
            ->when($term !== '', fn ($q) => $q->where(fn ($q) => $q
                ->whereLike('tracking_id', "%{$term}%")->orWhereLike('customer_name', "%{$term}%")->orWhereLike('customer_phone', "%{$term}%")))
            ->when($value('by') !== '', fn ($q) => $q->where('created_by', (int) $value('by')));
    }
}
