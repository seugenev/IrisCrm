<?php

namespace Importer\app\Models;

use Importer\app\core\Model;
use Importer\app\Structures\CustomFields;
use Importer\Report;

class Batch extends Model
{
    protected string $table = 'batches';

    protected array $fields = [
        Report::MERCHANT_ID,
        Report::BATCH_REF_NUM,
        Report::BATCH_DATE,
        CustomFields::BATCH_HASH,
    ];
}
