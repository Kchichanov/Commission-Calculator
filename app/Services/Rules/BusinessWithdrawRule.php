<?php

namespace App\Services\Rules;

use App\Helpers\Rounding;
use App\Services\Rules\RuleInterface;

class BusinessWithdrawRule implements RuleInterface
{
    public function calculate(array $operation): string
    {
        $fee = $operation['amount'] * 0.005;
        return Rounding::roundUp($fee, $operation['currency']);
    }
}
