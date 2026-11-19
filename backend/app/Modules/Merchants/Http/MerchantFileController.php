<?php

namespace App\Modules\Merchants\Http;

use App\Http\Controllers\Controller;
use App\Modules\Merchants\Models\Merchant;
use App\Modules\Merchants\Services\MerchantService;
use App\Shared\Http\PrivateFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Serves a merchant's private document after the route's merchant_view check. */
class MerchantFileController extends Controller
{
    public function __invoke(Merchant $merchant, string $column): StreamedResponse
    {
        abort_unless(array_key_exists($column, Merchant::FILES), 404);

        return PrivateFile::response(MerchantService::DISK, $merchant->{$column}, "{$merchant->unique_id}-".str_replace('_file', '', $column));
    }
}
