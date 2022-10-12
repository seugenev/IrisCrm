<?php

namespace Importer;

/**
 * Class Result
 */
class Result
{
    /** @var int Number of imported merchants */
    private int $merchants = 0;

    /** @var int Number of imported batches */
    private int $batches = 0;

    /** @var int Number of imported transactions */
    private int $transactions = 0;

    /** @var int Number of executed Queries to DB */
    private int $insertQueries = 0;


    /**
     * @param int $merchants
     */
    public function incrementMerchants(int $merchants): void
    {
        $this->merchants += $merchants;
    }

    /**
     * @param int $batches
     */
    public function incrementBatches(int $batches): void
    {
        $this->batches += $batches;
    }

    /**
     * @param int $transactions
     */
    public function incrementTransactions(int $transactions): void
    {
        $this->transactions += $transactions;
    }

    public function incrementInsertQueries(int $queries): void
    {
        $this->insertQueries += $queries;
    }

    /**
     * Gets a number of imported merchants
     *
     * @return int Number of imported merchants
     */
    public function getMerchantCount(): int
    {
        return $this->merchants;
    }

    /**
     * Gets a number of imported batches
     *
     * @return int Number of imported batches
     */
    public function getBatchCount(): int
    {
        return $this->batches;
    }

    /**
     * Gets a number of imported transactions
     *
     * @return int Number of imported transactions
     */
    public function getTransactionCount(): int
    {
        return $this->transactions;
    }

    public function getInsertQueriesCount(): int
    {
        return $this->insertQueries;
    }
}
