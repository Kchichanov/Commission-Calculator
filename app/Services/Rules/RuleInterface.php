<?php

namespace App\Services\Rules;

interface RuleInterface
{
    public function calculate(array $operation): string;
}
