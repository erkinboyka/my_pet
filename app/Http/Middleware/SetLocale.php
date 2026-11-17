<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    // язык из сессии, переключатель в шапке
    public function handle(Request $request, Closure $next): Response
    {
        $lang = session('lang', config('app.locale', 'ru'));

        if (in_array($lang, ['ru', 'en'], true)) {
            App::setLocale($lang);
        }

        return $next($request);
    }
}
