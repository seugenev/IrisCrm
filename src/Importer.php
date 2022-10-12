<?php

namespace Importer;

use Importer\app\core\Importer\Cache\MemoryCache;
use Importer\app\core\Importer\Strategies\ImporterInterface;
use Importer\app\core\Model;
use Importer\app\Models\Batch;
use Importer\app\Models\Merchant;
use Importer\app\Models\Transaction;
use Importer\app\Structures\CustomFields;
use Importer\app\Structures\PersistDto;

/**
 * Class Importer
 */
class Importer
{
    private Result $resultStats;

    /**
     * Importer constructor.
     */
    public function __construct()
    {
        // TODO: if needed change the constructor signature and initialize any properties/dependencies here
    }

    /**
     * Imports a given report
     *
     * @param ImporterInterface $importStrategy
     * @return Result Result of the import process
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \SodiumException
     */
    public function process(ImporterInterface $importStrategy): Result
    {
        $this->resultStats = new Result();

        $merchantModel = new Merchant();
        $batchModel = new Batch();
        $transModel = new Transaction();

        $merchantCache = new MemoryCache(Merchant::CACHE_SIZE);
        $batchCache = new MemoryCache();
        $transCache = new MemoryCache(Transaction::CACHE_SIZE);

        $generator = $importStrategy->importRow();

        $counter = 0;

        while ($generator->valid()) {
            $item = $generator->current();
            if (!$merchantCache->has($item[Report::MERCHANT_ID])
                && !$merchantModel->exists([Report::MERCHANT_ID => $item[Report::MERCHANT_ID]]))
            {
                if ($merchantCache->reachedMemoryLimit()) {
                    $persistDto = new PersistDto();
                    $persistDto
                        ->add($merchantCache, $merchantModel)
                        ->add($batchCache, $batchModel)
                        ->add($transCache, $transModel);
                    $this->persist($persistDto);
                    $persistDto = null;
                }
                $merchantCache->set($item[Report::MERCHANT_ID], [
                    Report::MERCHANT_ID => $item[Report::MERCHANT_ID],
                    Report::MERCHANT_NAME => $item[Report::MERCHANT_NAME]
                ]);
            }


            /**
             * field date with separators in the middle to avoid hash collisions
             * without separation, in case of concatenation "mid . ref . date"
             * these 2 sets will return same hash, that why important concatenate
             * or with separator like: "mid .-. ref .-. date"
             * or date in the middle like: "mid . date . ref"
             *
             * mid:123
             * ref: 1234
             * date: 15-02-2022

             * mid:1231
             * ref: 234
             * date: 15-02-2022
             */
            $batchKey = $item[Report::MERCHANT_ID] . $item[Report::BATCH_DATE] . $item[Report::BATCH_REF_NUM];
            $batchHash = sodium_bin2hex(sodium_crypto_generichash($batchKey, '', SODIUM_CRYPTO_GENERICHASH_BYTES_MIN));

            if (!$batchCache->has($batchHash)
                && !$batchModel->exists([
                    CustomFields::BATCH_HASH => $batchHash,
                ])
            ) {
                $batchCache->set($batchHash, [
                    Report::MERCHANT_ID => $item[Report::MERCHANT_ID],
                    Report::BATCH_REF_NUM => $item[Report::BATCH_REF_NUM],
                    Report::BATCH_DATE => $item[Report::BATCH_DATE],
                    CustomFields::BATCH_HASH => $batchHash,
                ]);
            }

            if ($transCache->reachedMemoryLimit()) {
                $persistDto = new PersistDto();
                $persistDto
                    ->add($merchantCache, $merchantModel)
                    ->add($batchCache, $batchModel)
                    ->add($transCache, $transModel);
                $this->persist($persistDto);
                $persistDto = null;
            }

            $transCache->set($counter++, [
                Report::TRANSACTION_CARD_NUMBER => $item[Report::TRANSACTION_CARD_NUMBER],
                Report::TRANSACTION_CARD_TYPE => $item[Report::TRANSACTION_CARD_TYPE],
                Report::TRANSACTION_DATE => $item[Report::TRANSACTION_DATE],
                Report::TRANSACTION_TYPE => $item[Report::TRANSACTION_TYPE],
                Report::TRANSACTION_AMOUNT => $item[Report::TRANSACTION_AMOUNT],
                CustomFields::BATCH_HASH => $batchHash,
            ]);


            $generator->next();

            if (!$generator->valid()) {
                $persistDto = new PersistDto();
                $persistDto
                    ->add($merchantCache, $merchantModel)
                    ->add($batchCache, $batchModel)
                    ->add($transCache, $transModel);
                $this->persist($persistDto);
                $persistDto = null;
            }
        }

        return $this->resultStats;
    }


    private function store(Model $model, $data): void
    {
        if (!empty($data)) {
            $model->insert($data);

            $methodName = 'increment' . ucfirst($model->getTableName());
            $this->resultStats->$methodName(count($data));
            $this->resultStats->incrementInsertQueries(1);
        }
    }

    public function persist(PersistDto $persistDto): void
    {
        foreach ($persistDto->get() as $dataSet) {
            $merchData = $dataSet['cache']->flush();
            $this->store($dataSet['model'], $merchData);
        }
    }

}
