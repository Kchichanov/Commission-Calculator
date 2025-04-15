<?php

namespace App\Helpers;

class Rounding
{
    protected static array $precision = [
        'EUR' => 2,
        'USD' => 2,
        'JPY' => 0,
    ];

    public static function roundUp(float $amount, string $currency): string
    {
        $decimals = self::$precision[$currency] ?? 2;
        $factor = pow(10, $decimals);
        return number_format(ceil($amount * $factor) / $factor, $decimals, '.', '');
    }
}
