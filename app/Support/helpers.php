<?php

// хелперы для цен и настроек юзера

if (! function_exists('current_currency')) {
    // валюта: TJS (сомони) или USD
    function current_currency(): string
    {
        $code = session('currency', 'TJS');

        if (! in_array($code, ['TJS', 'USD'], true)) {
            $code = 'TJS';
        }

        return $code;
    }
}

if (! function_exists('format_price')) {
    // суммы в базе в сомони, в долларах показываем по курсу из конфига
    function format_price(float $amount): string
    {
        if (current_currency() === 'USD') {
            $rate = (float) config('currency.rate', 9.7);

            return '$ '.number_format($amount / $rate, 2, '.', ' ');
        }

        return number_format($amount, 0, ',', ' ').' '.__('currency.tjs');
    }
}
