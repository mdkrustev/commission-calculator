<?php

namespace App\CommissionTask;

class OperationModel
{
    const OPERATION_WITHDRAW = 'withdraw';
    const OPERATION_DEPOSIT = 'deposit';
    const CLIENT_PRIVATE = 'private';
    const CLIENT_BUSINESS = 'business';
    const DEPOSIT_CHARGE_RATE = 0.03;
    const WITHDRAW_PRIVATE_CHARGE_RATE = 0.3;
    const WITHDRAW_BUSINESS_CHARGE_RATE = 0.5;
    const COUNT_OF_FREE_WITHDRAW_CHARGE = 3;
    const LIMIT_AMOUNT_OF_FREE_WITHDRAW_CHARGE = 1000;

    private $operationId;
    private $date;
    private $clientId;
    private $clientType;
    private $operationType;
    private $amount;
    private $currency;
    private $commission;

    /**
     * @return integer
     */
    public function getOperationId()
    {
        return $this->operationId;
    }

    /**
     * @param integer $operationId
     */
    public function setOperationId($operationId)
    {
        $this->operationId = $operationId;
    }

    /**
     * @param null | string $format
     * @return \DateTime | string
     * @throws \Exception
     */
    public function getDate($format = null)
    {
        if ($format)
            return (new \DateTime($this->date))->format($format);
        return new \DateTime($this->date);
    }

    public function getWeekStartAndEndDates()
    {
        $weekDayNumber = $this->getDate('w');
        if($weekDayNumber == 0) {
            $df = new \DateTime($this->getDate('Y-m-d'));
            $start = $df->modify("-6 days")->format('Y-m-d');
            $end = $this->getDate('Y-m-d');
        } else {
            $days = ((int)$this->getDate('w') - 1);
            $days = $days < 0 ? 0 : $days;
            $df = new \DateTime($this->getDate('Y-m-d'));
            $start = $df->modify("- $days days")->format('Y-m-d');
            $dt = new \DateTime($start);
            $end = $dt->modify("+ 6 days")->format('Y-m-d');
        }
        return $start . '_' . $end;
    }

    public function getOperationKey()
    {
        return
            $this->getClientId() . '-' .
            $this->getOperationType() . '-' .
            $this->getWeekStartAndEndDates();
    }

    /**
     * @param integer | double $commission
     */
    public function setCommission($commission = 0)
    {
        // Round the value and set it to string
        $this->commission = $commission;
    }

    /** @return string */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * @param string $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return integer
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param integer $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @return string
     */
    public function getClientType()
    {
        return $this->clientType;
    }

    /**
     * @param  string $clientType
     */
    public function setClientType($clientType)
    {
        $this->clientType = $clientType;
    }

    /**
     * @return string
     */
    public function getOperationType()
    {
        return $this->operationType;
    }

    /**
     * @param string $operationType
     */
    public function setOperationType($operationType)
    {
        $this->operationType = $operationType;
    }

    /**
     * @return double
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param double $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }
}
