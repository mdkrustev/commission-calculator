<?php
require_once 'vendor/autoload.php';
$commissionCalculator = new \App\CommissionTask\CommissionCalculator();
$commissionCalculator->calculate();
