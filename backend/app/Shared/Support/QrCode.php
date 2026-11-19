<?php

namespace App\Shared\Support;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

/** Inline SVG QR codes for printable labels (box labels now, parcel labels later). */
class QrCode
{
    public static function svg(string $data, int $size = 160): string
    {
        $svg = (new Writer(new ImageRenderer(new RendererStyle($size, 1), new SvgImageBackEnd)))->writeString($data);

        return preg_replace('/^<\?xml[^>]*\?>\s*/', '', $svg);
    }
}
