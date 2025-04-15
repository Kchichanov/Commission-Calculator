<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use App\Services\CommissionCalculator;

class CommissionCalculatorTest extends TestCase
{
    public function test_it_calculates_commission_correctly()
    {
        $csvPath = storage_path('app/input.csv');
        $operations = array_map('str_getcsv', file($csvPath));

        $expected = [
            "0.60",
            "3.00",
            "0.00",
            "0.06",
            "1.50",
            "0",
            "0.70",
            "0.30",
            "0.30",
            "3.00",
            "0.00",
            "0.00",
            "8612",
        ];

        $calculator = app(CommissionCalculator::class);
        $actual = $calculator->calculate($operations);

        $this->assertEquals($expected, $actual);
    }
}
