<?php

namespace Importer\app\core\DB;

use Importer\app\core\Model;
use PDO;

class Builder
{
    protected PDO $pdo;
    protected Model $model;

    public function __construct(PDO $pdo, Model $model)
    {
        $this->pdo = $pdo;
        $this->model = $model;
    }

    public function tableExists(string $tableName): bool
    {
        try {
            $result = $this->pdo->query("SELECT 1 FROM ".$this->model->getTableName()." LIMIT 1");
        } catch (\Exception $e) {
            return false;
        }

        return $result !== false;
    }


    /**
     * @param array $data ['field_1' => value_1, 'field_2' => 'value_2', ...]
     * @param string $glue
     * @return bool
     * @throws \Importer\app\Exceptions\DatabaseException
     */
    public function exists(array $data, string $glue = 'AND'): bool
    {
        if (empty($data)) {
            return false;
        }

        $where = [];
        $values = [];
        foreach ($data as $field => $value) {
            $where[] = ' `' . $field . '` = ? ';
            $values[] = $value;
        }
        $where = implode($glue, $where);

        $sql = 'SELECT 1 '
                . ' FROM ' . $this->model->getTableName()
                . ' WHERE ' . $where . ' LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);

        if(!$stmt->fetchColumn())
            return false;

        return true;
    }

    /**
     * Inserts one or multiple lines at once
     * $data = [
     *   ['field_1' => 11111, 'field_2' => 'xxxxxx', ...],
     *   ['field_1' => 22222, 'field_2' => 'yyyyyy', ...],
     * ];
     * $data = ['field_1' => 11111, 'field_2' => 'xxxxxx', ...];
     *
     * @param array $data
     * @return int
     * @throws \Importer\app\Exceptions\DatabaseException
     */
    public function insert(array $data): int
    {
        if (empty($data)) {
            return true;
        }

        if (!is_array(reset($data))) {
            $data = [$data];
        }

        $fields = array_keys(current($data));

        $this->pdo->beginTransaction();
        $placeholders = $this->placeholders('?', sizeof($fields));
        $questionMarks = [];
        $insertValues = [];

        foreach($data as $d){
            $questionMarks[] = '('  . $placeholders . ')';
            $insertValues = array_merge($insertValues, array_values($d));
        }

        $sql = 'INSERT INTO ' . $this->model->getTableName()
                . ' (`' . implode("`,`", $fields ) . '`) '
                . 'VALUES ' . implode(',', $questionMarks);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($insertValues);
        $insertId = $this->pdo->lastInsertId();
        $this->pdo->commit();

        return $insertId;
    }


    private function placeholders(string $text, int $count = 0, string $separator = ','): string
    {
        $result = [];
        if($count > 0){
            for($x=0; $x<$count; $x++){
                $result[] = $text;
            }
        }

        return implode($separator, $result);
    }
}
