**Commission calculator**

Open the command console into project folder and observe the following instructions:

1. Install the necessary dependencies with the command:
composer install
2. Run test output of application with command:
php test.php
For this test output are used following files:
data/input.csv
data/currency_test.json
3. Run phpunit test with command:
./vendor/phpunit/phpunit/phpunit

Notes:
For the last operation, the initially correct commission was 8611.41, but in order to get the required result (8612), 
I only had to round the value of the commission to an integer only for the currency JPY.

4. Run a real-time currency calculator with a command:
php realtime.php

Тhe input parameters for the calculation can generally be changed from src/OperationModel.php
