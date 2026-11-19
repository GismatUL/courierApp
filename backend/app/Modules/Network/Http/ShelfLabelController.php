<?php

namespace App\Modules\Network\Http;

use App\Http\Controllers\Controller;
use App\Modules\Network\Models\Shelf;
use App\Shared\Support\QrCode;
use Illuminate\View\View;

/** Printable box label: name, zone, code and a QR of the code (spec §5.2 /admin/shelf/print/{id}). */
class ShelfLabelController extends Controller
{
    public function __invoke(Shelf $shelf): View
    {
        return view('network::shelf-label', ['shelf' => $shelf->load('zone'), 'qr' => QrCode::svg($shelf->code, 220)]);
    }
}
