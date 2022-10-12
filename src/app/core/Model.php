<?php

namespace Importer\app\core;

use Importer\app\core\DB\Builder;
use Importer\app\core\DB\Connection;
use Importer\app\Exceptions\DatabaseException;
use PDO;
use Symfony\Component\String\Inflector\EnglishInflector;

abstract class Model
{
    /**
     * number of entries cached
     */
    public const CACHE_SIZE = 50;

    protected PDO $dbConnection;
    protected Builder $qBuilder;

    public function __construct()
    {
        $this->setDbConnection();
    }

    public function setDbConnection(PDO $dbConnection = null): self
    {
        if (!$dbConnection) {
            // database credentials are located in docker-compose.yml and loaded by phpdotenv package
            $this->dbConnection = Connection::getConnection(
                getenv('DB_HOST'),
                getenv('DB_NAME'),
                getenv('DB_USER'),
                getenv('DB_PASS')
            );
        } else {
            $this->dbConnection = $dbConnection;
        }

        $this->qBuilder = new Builder($this->dbConnection, $this);

        return $this;
    }

    public function getTableName(): string
    {
        if (!property_exists($this, 'table')) {
            $reflect = new \ReflectionClass($this);
            $tableName = (new EnglishInflector())->pluralize(strtolower($reflect->getShortName()))[0];

            if (!$this->qBuilder->tableExists($tableName)) {
                throw new DatabaseException('Model table name is not defined');
            }

            $this->table = $tableName;
        }

        return $this->table;
    }

    public function __call(string $method, array $parameters)
    {
        return $this->qBuilder->$method(...$parameters);
    }
}
