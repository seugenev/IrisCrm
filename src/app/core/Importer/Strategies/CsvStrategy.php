<?php

namespace Importer\app\core\Importer\Strategies;

use Generator;
use Importer\app\Exceptions\CsvException;

class CsvStrategy implements ImporterInterface
{
    private $fileHandle;
    private array $mapping;
    private string $delimiter;

    public function __construct(string $file, array $mapping, string $delimiter = ',')
    {
        $this->setFileHandle($file);
        $this->mapping = $mapping;
        $this->delimiter = $delimiter;
    }

    public function getOrderedHeader($headerColumns): array
    {
        $columnsOrder = [];
        foreach ($headerColumns as $fileColumn) {
            $dbColumn = array_search($fileColumn, $this->getMapping());
            if ($dbColumn !== false) {
                $columnsOrder[$fileColumn] = $dbColumn;
            }
        }

        if (count($columnsOrder) != count($this->getMapping())) {
            throw new CsvException("File doesn't match mapping");
        }

        return $columnsOrder;
    }


    public function importRow(): Generator
    {
        $header = [];
        $row = 0;

        while (($data = fgetcsv($this->getFileHandle(), 0, $this->delimiter)) !== false) {

            if ($row == 0) {
                $header = $this->getOrderedHeader($data);
            } else {
                yield array_combine($header, $data);
            }

            $row++;
        }
    }

    public function getMapping(): array
    {
        return $this->mapping;
    }

    public function getFileHandle()
    {
        return $this->fileHandle;
    }

    public function setFileHandle(string $file): void
    {
        $fileHandle = fopen($file, "r");
        if ($fileHandle === false) {
            throw new CsvException('Cannot read file');
        }

        $this->fileHandle = $fileHandle;
    }


}
