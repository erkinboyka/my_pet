<?php

/**
 * Небольшие помощники для отображения цен и настроек пользователя.
 */

if (! function_exists('current_currency')) {
    /**
     * Выбранная валюта: TJS (сомони) или USD.
     */
    function current_currency(): string
    {
        $code = session('currency', 'TJS');

        return in_array($code, ['TJS', 'USD'], true) ? $code : 'TJS';
    }
}

if (! function_exists('format_price')) {
    /**
     * Суммы в базе хранятся в сомони. В USD показываем по курсу из конфига.
     */
    function format_price(float $amount): string
    {
        if (current_currency() === 'USD') {
            $rate = (float) config('currency.rate', 9.7);

            return '$ '.number_format($amount / $rate, 2, '.', ' ');
        }

        return number_format($amount, 0, ',', ' ').' '.__('currency.tjs');
    }
}
