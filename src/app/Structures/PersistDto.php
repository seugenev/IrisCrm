<?php

namespace Importer\app\Structures;

use Generator;
use Importer\app\core\Model;
use Psr\SimpleCache\CacheInterface;

class PersistDto
{
    private array $container = [];

    public function add(CacheInterface $cache, Model $model): self
    {
        $this->container[] = [
            'cache' => $cache,
            'model' => $model
        ];
        return $this;
    }

    public function get(): Generator
    {
        foreach ($this->container as $dataSet) {
            yield $dataSet;
        }
    }
}
