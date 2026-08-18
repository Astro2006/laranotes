<?php

namespace App\Http\Controllers;

use App\Enums\Locale;
use Illuminate\Http\RedirectResponse;

class SetLocaleController extends Controller
{
    /**
     * Persist the selected locale in the session and redirect back.
     */
    public function __invoke(Locale $locale): RedirectResponse
    {
        session(['locale' => $locale->value]);

        return back(fallback: route('notes.index'));
    }
}
