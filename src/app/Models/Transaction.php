<?php

namespace Importer\app\Models;

use Importer\app\core\Model;
use Importer\app\Structures\CustomFields;
use Importer\Report;

class Transaction extends Model
{
    protected string $table = 'transactions';

    protected array $fields = [
        Report::TRANSACTION_CARD_NUMBER,
        Report::TRANSACTION_CARD_TYPE,
        Report::TRANSACTION_DATE,
        Report::TRANSACTION_TYPE,
        Report::TRANSACTION_AMOUNT,
        CustomFields::BATCH_HASH,
    ];

    /**
     * number of entries cached
     */
    public const CACHE_SIZE = 200;

}
