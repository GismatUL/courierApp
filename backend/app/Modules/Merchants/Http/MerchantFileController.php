<?php

namespace App\Modules\Merchants\Http;

use App\Http\Controllers\Controller;
use App\Modules\Merchants\Models\Merchant;
use App\Modules\Merchants\Services\MerchantService;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Serves a merchant's private document after the route's merchant_view check. Only images open inline;
 * everything else downloads. Never cached, never sniffed (security rules: Files / Downloads).
 */
class MerchantFileController extends Controller
{
    private const INLINE = ['image/png', 'image/jpeg', 'image/webp'];

    public function __invoke(Merchant $merchant, string $column): StreamedResponse
    {
        abort_unless(array_key_exists($column, Merchant::FILES) && $merchant->{$column}, 404);

        $disk = Storage::disk(MerchantService::DISK);
        abort_unless($disk->exists($merchant->{$column}), 404);

        $mime = $disk->mimeType($merchant->{$column}) ?: 'application/octet-stream';
        $name = "{$merchant->unique_id}-".str_replace('_file', '', $column).'.'.pathinfo($merchant->{$column}, PATHINFO_EXTENSION);
        $headers = ['X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'no-store, private'];

        return in_array($mime, self::INLINE, true)
            ? $disk->response($merchant->{$column}, $name, ['Content-Type' => $mime] + $headers, 'inline')
            : $disk->download($merchant->{$column}, $name, $headers);
    }
}
