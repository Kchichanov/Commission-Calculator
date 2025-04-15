<?php

namespace App\Services;

class CurrencyConverter
{
    // Hardcoded in this case because to match the example API call seemed unnecessary
    protected array $ratesToEUR = [
        'EUR' => 1,
        'USD' => 1 / 1.1497,
        'JPY' => 1 / 129.53,
    ];

    public function toEUR(float $amount, string $currency): float
    {
        if (!isset($this->ratesToEUR[$currency])) {
            throw new \Exception("Currency rate not defined: $currency");
        }

        return $amount * $this->ratesToEUR[$currency];
    }
}
