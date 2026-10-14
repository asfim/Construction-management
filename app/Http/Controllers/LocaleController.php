<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    /**
     * Switch language between bn and en.
     */
    public function switch(string $locale, Request $request): RedirectResponse
    {
        if (in_array($locale, ['bn', 'en'])) {
            Session::put('locale', $locale);

            if (auth()->check()) {
                auth()->user()->updateQuietly(['preferred_locale' => $locale]);
            }
        }

        return back();
    }
}
