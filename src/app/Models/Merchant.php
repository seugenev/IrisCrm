<?php

namespace Importer\app\Models;

use Importer\app\core\Model;
use Importer\Report;

class Merchant extends Model
{
    protected string $table = 'merchants';

    protected array $fields = [
        Report::MERCHANT_ID,
        Report::MERCHANT_NAME
    ];

    /**
     * number of entries cached
     */
    public const CACHE_SIZE = 50;
}
