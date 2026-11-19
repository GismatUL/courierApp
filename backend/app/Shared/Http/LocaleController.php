<?php

namespace App\Shared\Http;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, SetLocale::LOCALES, true), 404);
        $request->session()->put('locale', $locale);

        return back();
    }
}
