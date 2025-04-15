<?php
namespace App\Services\Rules;

use App\Helpers\Rounding;
use App\Services\CurrencyConverter;
use Carbon\Carbon;
use App\Services\Rules\RuleInterface;

class PrivateWithdrawRule implements RuleInterface
{
    protected CurrencyConverter $converter;

    protected array $userWeekTracker = [];

    public function __construct(CurrencyConverter $converter)
    {
        $this->converter = $converter;
    }

    public function calculate(array $operation): string
    {
        $userId = $operation['user_id'];
        $date = Carbon::parse($operation['date']);
        $weekKey = $date->format('o-W');
        $key = $userId . '-' . $weekKey;

        $amount = $operation['amount'];
        $currency = $operation['currency'];

        $amountInEUR = $this->converter->toEUR($amount, $currency);

        if (!isset($this->userWeekTracker[$key])) {
            $this->userWeekTracker[$key] = [
                'count' => 0,
                'used_eur' => 0.0,
            ];
        }

        $userData = &$this->userWeekTracker[$key];
        $userData['count']++;

        $freeLimit = 1000.0;
        $commissionRate = 0.003;
        $fee = 0.0;

        if ($userData['count'] <= 3) {
            $remainingFree = max(0.0, $freeLimit - $userData['used_eur']);

            if ($amountInEUR <= $remainingFree) {
                $userData['used_eur'] += $amountInEUR;
                return Rounding::roundUp(0.0, $currency);
            } else {
                $commissionableEUR = $amountInEUR - $remainingFree;
                $userData['used_eur'] = $freeLimit;

                // Convert commissionable EUR back to original currency
                $commissionableOriginal = $commissionableEUR / $this->converter->toEUR(1, $currency);
                $fee = $commissionableOriginal * $commissionRate;
            }
        } else {
            $fee = $amount * $commissionRate;
        }

        return Rounding::roundUp($fee, $currency);
    }
}
