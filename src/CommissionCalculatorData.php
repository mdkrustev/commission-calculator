<?php

namespace App\CommissionTask;

class CommissionCalculatorData
{
    const BASE_CURRENCY = 'EUR';
    const ACCESS_CURRENCY_KEY = '6ad25f8c043804ac90b182871d13a9b7';

    /**
     * @return array;
     */
    public function operationsData()
    {
        /* 1. Data retrieving from csv file. */
        $inputData = array_map('str_getcsv', file(__DIR__ . '/../data/input.csv'));

        $operations = [];
        $groupedOperations = [];
        foreach ($inputData as $i => $data) {
            if (sizeof($data) != 6) break;

            /* 2. Map data to input data model */
            $operationData = new OperationModel();
            $operationData->setOperationId(++$i);
            $operationData->setDate($data[0]);
            $operationData->setClientId($data[1]);
            $operationData->setClientType($data[2]);
            $operationData->setOperationType($data[3]);
            $operationData->setAmount($data[4]);
            $operationData->setCurrency($data[5]);

            /* 3. Prepare array with operationId as a key and empty values  */
            $operations[$operationData->getOperationId()] = null;

            /* 4. Group data by operation key (client id, operation type, week start and end dates  */
            $groupedOperations[$operationData->getOperationKey()][] = $operationData;
        }
        return [
            'operations' => $operations,
            'groupedOperations' => $groupedOperations
        ];
    }

    public function currencyData($type)
    {
        $real_time = 'http://api.exchangeratesapi.io/v1/latest?access_key='.self::ACCESS_CURRENCY_KEY.'&base=' . self::BASE_CURRENCY;
        $test = __DIR__ . '/../data/currency_test.json';
        return json_decode(file_get_contents($$type));
    }
}
