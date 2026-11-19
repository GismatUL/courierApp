<?php

namespace App\Shared\Http;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Applies the panel language chosen in the top bar (spec §2.1: az, en, ru, tr). */
class SetLocale
{
    public const LOCALES = ['az', 'en', 'ru', 'tr'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale');

        if (in_array($locale, self::LOCALES, true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
