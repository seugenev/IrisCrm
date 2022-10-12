<?php

namespace Importer\app\core\Importer\Cache;

use Psr\SimpleCache\CacheInterface;

class MemoryCache implements CacheInterface
{
    protected ?int $memoryLimit;
    protected array $cache = [];

    public function __construct(int $memoryLimit = null)
    {
        $this->memoryLimit = $memoryLimit;
    }

    public function clear(): bool
    {
        $this->cache = [];

        return true;
    }

    public function delete($key): bool
    {
        unset($this->cache[$key]);

        return true;
    }

    public function deleteMultiple($keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

    public function get($key, $default = null): mixed
    {
        if ($this->has($key)) {
            return $this->cache[$key];
        }

        return $default;
    }

    public function getMultiple($keys, $default = null): array
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->get($key, $default);
        }

        return $results;
    }

    public function has($key): bool
    {
        return isset($this->cache[$key]);
    }

    public function set($key, $value, $ttl = null): bool
    {
        $this->cache[$key] = $value;

        return true;
    }

    public function setMultiple($values, $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }

        return true;
    }

    public function reachedMemoryLimit(): bool
    {
        // When no limit is given, we'll never reach any limit.
        if (null === $this->memoryLimit) {
            return false;
        }

        return count($this->cache) >= $this->memoryLimit;
    }

    public function flush(): array
    {
        $memory = $this->cache;

        $this->clear();

        return $memory;
    }
}
