<?php

namespace App\Modules\Parcels\Http;

use App\Http\Controllers\Controller;
use App\Modules\Parcels\Livewire\ImportParcels;
use App\Modules\Parcels\Models\Parcel;
use App\Modules\Parcels\Services\ParcelFilters;
use App\Shared\Support\QrCode;
use App\Shared\Support\Spreadsheet;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Labels (spec §4.6), Excel export of the filtered list (§4.8) and the import template (§4.7). */
class ParcelFileController extends Controller
{
    public function labels(Request $request): View
    {
        $ids = array_slice(array_filter(array_map('intval', explode(',', (string) $request->query('ids')))), 0, 500);
        abort_if($ids === [], 404);

        $parcels = Parcel::with(['merchant:id,business_name,mobile', 'zone:id,name'])->whereIn('id', $ids)->orderBy('id')->get();

        return view('parcels::labels', [
            'parcels' => $parcels,
            'qr' => $parcels->mapWithKeys(fn ($p) => [$p->id => QrCode::svg($p->tracking_id, 140)]),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $rows = ParcelFilters::apply($request->query())
            ->with(['merchant:id,business_name', 'zone:id,name', 'deliveryMan.user:id,name'])
            ->orderByDesc('id')
            ->lazy(500)
            ->map(fn (Parcel $p) => [
                $p->tracking_id, $p->invoice_no, $p->merchant_invoice_no, $p->merchant->business_name, $p->customer_name,
                $p->customer_phone, $p->customer_address, $p->zone->name, $p->status->label(), $p->deliveryMan?->user->name,
                (float) $p->delivery_charge, $p->parcel_count, (float) $p->cash_collection, (float) $p->total,
                $p->created_date?->timezone('Asia/Baku')->format('Y-m-d H:i'),
            ]);

        return Spreadsheet::download('paketler-'.now('Asia/Baku')->format('Y-m-d-His').'.xlsx', [
            'İzləmə kodu', 'Qaimə №', 'Partnyor qaiməsi', 'Partnyor', 'Müştəri', 'Telefon', 'Ünvan', 'Zona', 'Status', 'Kuryer',
            'Çatdırılma qiyməti', 'Say', 'Nağd toplama', 'Ümumi', 'Tarix',
        ], $rows);
    }

    public function importTemplate(): StreamedResponse
    {
        return Spreadsheet::download('paket-import-numune.xlsx', array_values(ImportParcels::COLUMNS), [
            ['Əli Məmmədov', '994501234567', 'Bakı, Nizami küç. 10', '40.4093', '49.8671', '1-ci zona', 'Zərf', 'Regular', '1', '0.3', '25', 'INV-1001', ''],
        ]);
    }
}
