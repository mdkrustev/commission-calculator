<?php

namespace App\CommissionTask;

class CommissionCalculator
{
    private $data;
    private $groupedOperations;
    private $operations;
    private $currencyRates;
    private $freeWithdrawCounter = [];

    public function __construct()
    {
        $this->data = new CommissionCalculatorData();
        $this->groupedOperations = $this->data->operationsData()['groupedOperations'];
        $this->operations = $this->data->operationsData()['operations'];
    }

    public function calculate()
    {
        $this->currencyRates = $this->data->currencyData("real_time")->{'rates'};

        foreach ($this->groupedOperations as $key => $groupedOperations) {
            /** @var OperationModel $operation */
            foreach ($groupedOperations as $i => $operation) {
                $operation->setCommission($this->commissionCalculation($operation, ++$i));
                $this->operations[$operation->getOperationId()] = $operation;
            }
        }
        /** @var OperationModel $o */
        foreach ($this->operations as $o) {
            echo $o->getCommission() . "\r\n";
        }
    }

    public function calculateTest($output = false)
    {
        $this->currencyRates = $this->data->currencyData("test")->{'rates'};
        foreach ($this->groupedOperations as $key => $groupedOperations) {
            /** @var OperationModel $operation */
            foreach ($groupedOperations as $i => $operation) {
                $operation->setCommission($this->commissionCalculation($operation, ++$i));
                $this->operations[$operation->getOperationId()] = $operation;
            }
        }
        $testCommissions = [];
        /** @var OperationModel $o */
        foreach ($this->operations as $o) {
            $testCommissions[] = $o->getCommission();
            if ($output)
                echo $o->getCommission() . "\r\n";
        }
        return $testCommissions;
    }

    private function calculateCurrencyToBase($amount, $currency)
    {
        if (isset($this->currencyRates->{$currency}))
            return $amount / $this->currencyRates->{$currency};
        return 0;
    }

    private function calculateBaseToCurrency($amount, $currency)
    {
        if (isset($this->currencyRates->{$currency}))
            return $amount * $this->currencyRates->{$currency};
        return 0;
    }

    /**
     * @param OperationModel $operation
     * @param $number
     * @return float|int
     */
    private function commissionCalculation(OperationModel $operation, $number)
    {
        $amount = $operation->getAmount();
        $commission = 0;
        switch ($operation->getOperationType()) {
            case OperationModel::OPERATION_DEPOSIT:
                $commission = $amount * (OperationModel::DEPOSIT_CHARGE_RATE / 100);
                break;
            case OperationModel::OPERATION_WITHDRAW:
                if ($operation->getClientType() == OperationModel::CLIENT_BUSINESS)
                    $commission = $amount * (OperationModel::WITHDRAW_BUSINESS_CHARGE_RATE / 100);
                if ($operation->getClientType() == OperationModel::CLIENT_PRIVATE) {
                    if ($number <= OperationModel::COUNT_OF_FREE_WITHDRAW_CHARGE) {
                        list($exceededAmount, $currentSumAmount) = [0, 0];

                        //Collect first 3 operation amounts of the week.
                        $calculatedAmount = $this->calculateCurrencyToBase($amount, $operation->getCurrency());
                        $this->freeWithdrawCounter[$operation->getOperationKey()][] = $calculatedAmount;


                        if (isset($this->freeWithdrawCounter[$operation->getOperationKey()]))
                            $currentSumAmount = array_sum($this->freeWithdrawCounter[$operation->getOperationKey()]);

                        //Calculation of exceeded amount
                        if ($currentSumAmount > OperationModel::LIMIT_AMOUNT_OF_FREE_WITHDRAW_CHARGE) {
                            if (($currentSumAmount - $calculatedAmount) >= OperationModel::LIMIT_AMOUNT_OF_FREE_WITHDRAW_CHARGE) {
                                $exceededAmount = $this->calculateBaseToCurrency($calculatedAmount, $operation->getCurrency());
                            } else {
                                $exceededAmount = $this->calculateBaseToCurrency($currentSumAmount - OperationModel::LIMIT_AMOUNT_OF_FREE_WITHDRAW_CHARGE, $operation->getCurrency());
                            }
                        }
                        $commission = $exceededAmount * (OperationModel::WITHDRAW_PRIVATE_CHARGE_RATE / 100);
                        if ($operation->getCurrency() == 'JPY')
                            $commission = ceil($commission);
                    } else {
                        $commission = $amount * (OperationModel::WITHDRAW_PRIVATE_CHARGE_RATE / 100);
                        $commission = $this->calculateCurrencyToBase($commission, $operation->getCurrency());
                    }
                }
                break;
        }
        return round(round($commission, 3), 2);
    }


}
