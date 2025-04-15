<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CommissionCalculator;

class CalculateCommission extends Command
{
    protected $signature = 'calculate:commission {file}';
    protected $description = 'Calculates commission fees from a CSV input file';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(CommissionCalculator $calculator)
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: $filePath");
            return Command::FAILURE;
        }

        $operations = array_filter(
            array_map('str_getcsv', file($filePath)),
            fn($row) => count($row) >= 6
        );

        $results = $calculator->calculate($operations);

        foreach ($results as $fee) {
            $this->line($fee);
        }

        return Command::SUCCESS;
    }
}
