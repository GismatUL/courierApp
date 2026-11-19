<?php

namespace App\Shared\Http;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Response for a private stored file, after the caller has checked permission.
 * Only PNG/JPEG/WEBP open inline; everything else downloads. Never cached, never sniffed.
 */
class PrivateFile
{
    private const INLINE = ['image/png', 'image/jpeg', 'image/webp'];

    public static function response(string $disk, ?string $path, string $downloadBaseName): StreamedResponse
    {
        $storage = Storage::disk($disk);
        abort_unless($path && $storage->exists($path), 404);

        $mime = $storage->mimeType($path) ?: 'application/octet-stream';
        $name = $downloadBaseName.'.'.pathinfo($path, PATHINFO_EXTENSION);
        $headers = ['X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'no-store, private'];

        return in_array($mime, self::INLINE, true)
            ? $storage->response($path, $name, ['Content-Type' => $mime] + $headers, 'inline')
            : $storage->download($path, $name, $headers);
    }
}
