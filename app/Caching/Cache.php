<?php

namespace App\Caching;

use App\Interfaces\ICache;
use App\Logger\Logger;
use App\Logger\LogLevel;
use http\Exception\InvalidArgumentException;
use Memcached;

class Cache implements ICache
{
    private readonly Memcached $memcached;
    private readonly Logger $logger;
    private const int DEFAULT_TTL_SECONDS = 600;

    public function __construct(Memcached $memcached, Logger $logger)
    {
        $this->memcached = $memcached;
        $this->logger = $logger;
    }

    public function get($key, $default = null) : mixed
    {
        if (!isset($key)) {
            throw new InvalidArgumentException('Cache key must not be null.');
        }

        if ($this->memcached->get($key) === false)
        {
            $this->logger->log(LogLevel::CRITICAL, "Cache key '{$key}' does not exist.");
            return $default;
        }

        return $this->memcached->get($key);
    }

    public function set($key, $value, $ttl = self::DEFAULT_TTL_SECONDS) : void
    {
        if (!isset($key)) {
            throw new InvalidArgumentException("Key is null when setting to cache.");
        }

        $this->memcached->set($key, $value, $ttl);
    }

    public function delete($key) : void
    {
        if (!isset($key)) {
            throw new InvalidArgumentException('Cache key must not be null.');
        }

        $this->memcached->delete($key);
    }

    public function clear() : void
    {
        $this->memcached->flush();
    }

    public function getMultiple(array $keys, $default = null)
    {
        if (!is_iterable($keys)) {
            throw new InvalidArgumentException('Cache keys must be iterable.');
        }

        if ($this->memcached->getMulti($keys) === false)
        {
            $this->logger->log(LogLevel::CRITICAL, "Cache method 'GetMulti' failed.");
            return $default;
        }

        return $this->memcached->getMulti($keys);
    }

    public function setMultiple(array $values, $ttl = self::DEFAULT_TTL_SECONDS) : void
    {
        if (!is_iterable($values)) {
            throw new InvalidArgumentException('Cache values must be iterable.');
        }

        $this->memcached->setMulti($values, $ttl);
    }

    public function deleteMultiple(array $keys) : void
    {
        if (!is_iterable($keys)) {
            throw new InvalidArgumentException('Cache keys must be iterable.');
        }

        $this->memcached->deleteMulti($keys);
    }

    public function has($key) : bool
    {
        if (!isset($key)) {
            throw new InvalidArgumentException('Cache key must not be null.');
        }

        if ($this->memcached->get($key) === false)
        {
            $this->logger->log(LogLevel::ALERT, "Cache does not have the key '{$key}'.");
            return false;
        }

        return true;
    }
}