<?php
declare(strict_types=1);

namespace App\Caching;

use App\Logger\Logger;
use App\Logger\LogLevel;
use Memcached;

class Cache implements CacheInterface
{
    private const int DEFAULT_TTL_SECONDS = 600;

    public function __construct(
        private readonly Memcached $memcached,
        private readonly Logger $logger
    ){
    }

    public function get(int|string|float $key, $default = null): mixed
    {
        if ($this->memcached->get($key) === false) {
            $this->logger->log(LogLevel::DEBUG, "Cache key '{$key}' does not exist.");
            return $default;
        }

        return $this->memcached->get($key);
    }

    public function set(int|string|float  $key, int|string|float  $value, $ttl = self::DEFAULT_TTL_SECONDS): void
    {
        $this->memcached->set($key, $value, $ttl);
    }

    public function delete(int|string|float  $key): void
    {
        $this->memcached->delete($key);
    }

    public function clear(): void
    {
        $this->memcached->flush();
    }

    public function getMultiple(array $keys, $default = null): mixed
    {
        if ($this->memcached->getMulti($keys) === false) {
            $this->logger->log(LogLevel::DEBUG, "Cache method 'GetMulti' failed.");
            return $default;
        }

        return $this->memcached->getMulti($keys);
    }

    public function setMultiple(array $values, $ttl = self::DEFAULT_TTL_SECONDS): void
    {
        $this->memcached->setMulti($values, $ttl);
    }

    public function deleteMultiple(array $keys): void
    {
        $this->memcached->deleteMulti($keys);
    }

    public function has(int|string|float $key): bool
    {
        if ($this->memcached->get($key) === false) {
            $this->logger->log(LogLevel::DEBUG, "Cache does not have the key '{$key}'.");
            return false;
        }

        return true;
    }
}