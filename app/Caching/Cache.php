<?php

namespace App\Caching;

use App\Interfaces\ICache;
use App\Logger\Logger;
use App\Logger\LogLevel;
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
        if ($this->memcached->get($key) === false)
        {
            $this->logger->log(LogLevel::CRITICAL, "Cache key '{$key}' does not exist.");
            return $default;
        }

        return $this->memcached->get($key);
    }

    public function set($key, $value, $ttl = self::DEFAULT_TTL_SECONDS) : void
    {
        $this->memcached->set($key, $value, $ttl);
    }

    public function delete($key) : void
    {
        $this->memcached->delete($key);
    }

    public function clear() : void
    {
        $this->memcached->flush();
    }

    public function getMultiple(array $keys, $default = null)
    {
        if ($this->memcached->getMulti($keys) === false)
        {
            $this->logger->log(LogLevel::CRITICAL, "Cache method 'GetMulti' failed.");
            return $default;
        }

        return $this->memcached->getMulti($keys);
    }

    public function setMultiple(array $values, $ttl = self::DEFAULT_TTL_SECONDS) : void
    {
        $this->memcached->setMulti($values, $ttl);
    }

    public function deleteMultiple(array $keys) : void
    {
        $this->memcached->deleteMulti($keys);
    }

    public function has($key) : bool
    {
        if ($this->memcached->get($key) === false)
        {
            $this->logger->log(LogLevel::CRITICAL, "Cache does not have the key '{$key}'.");
            return false;
        }

        return true;
    }
}