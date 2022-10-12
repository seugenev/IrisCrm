<?php

use Dotenv\Dotenv;
use Importer\app\core\Importer\Strategies\CsvStrategy;
use Importer\Importer;
use Importer\Report;

include __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$filename = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'report.csv';
$mapping  = [
    Report::TRANSACTION_DATE        => 'Transaction Date',
    Report::TRANSACTION_TYPE        => 'Transaction Type',
    Report::TRANSACTION_CARD_TYPE   => 'Transaction Card Type',
    Report::TRANSACTION_CARD_NUMBER => 'Transaction Card Number',
    Report::TRANSACTION_AMOUNT      => 'Transaction Amount',
    Report::BATCH_DATE              => 'Batch Date',
    Report::BATCH_REF_NUM           => 'Batch Reference Number',
    Report::MERCHANT_ID             => 'Merchant ID',
    Report::MERCHANT_NAME           => 'Merchant Name',
];

$time_start = microtime(true)*1000;

$importStrategy = new CsvStrategy($filename, $mapping);

$importer = new Importer();
try {
    $result = $importer->process($importStrategy);
} catch (\Throwable $e) {
    echo $e->getMessage();
}


$time_end = microtime(true)*1000;

echo sprintf(
    "Imported %d merchants, %d batches, and %d transactions.\n%d DB Insert queries executed" . PHP_EOL,
    $result->getMerchantCount(),
    $result->getBatchCount(),
    $result->getTransactionCount(),
    $result->getInsertQueriesCount(),
);
$execution_time = $time_end - $time_start;
echo 'Total Execution Time: ' . $execution_time . " ms\n";
