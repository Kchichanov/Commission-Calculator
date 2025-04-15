<?php

namespace App\Services;

use App\Services\Rules\DepositRule;
use App\Services\Rules\PrivateWithdrawRule;
use App\Services\Rules\BusinessWithdrawRule;

class CommissionCalculator
{
    protected $depositRule;
    protected $privateWithdrawRule;
    protected $businessWithdrawRule;

    public function __construct(DepositRule $depositRule, PrivateWithdrawRule $privateWithdrawRule, BusinessWithdrawRule $businessWithdrawRule) {

        $this->depositRule = $depositRule;
        $this->privateWithdrawRule = $privateWithdrawRule;
        $this->businessWithdrawRule = $businessWithdrawRule;
    }

    public function calculate(array $operations): array
    {
        $results = [];

        foreach ($operations as $line => $operation) {

            if (count($operation) !== 6) {
                throw new \Exception("Malformed CSV");
            }

            [$date, $userId, $userType, $operationType, $amount, $currency] = $operation;

            $data = [
                'date' => $date,
                'user_id' => (int) $userId,
                'user_type' => $userType,
                'operation_type' => $operationType,
                'amount' => (float) $amount,
                'currency' => $currency,
            ];

            if (!in_array($operationType, ['deposit', 'withdraw'])) {
                throw new \Exception("Unsupported operation type: $operationType");
            }

            if (!in_array($userType, ['private', 'business'])) {
                throw new \Exception("Unsupported user type: $userType");
            }

            // In a real world scenario this could be done with a Resolver.
            if ($operationType === 'deposit') {
                $fee = $this->depositRule->calculate($data);
            } elseif ($userType === 'private') {
                $fee = $this->privateWithdrawRule->calculate($data);
            } else {
                $fee = $this->businessWithdrawRule->calculate($data);
            }

            $results[] = $fee;
        }

        return $results;
    }
}
