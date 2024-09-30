<?php

declare(strict_types=1);

namespace App\Caching;

interface CacheInterface
{
    public function get(string $key, $default = null);
    public function set(string $key, string $value, $ttl = null);
    public function delete(string $key);
    public function clear();
    public function getMultiple(array $keys, $default = null);
    public function setMultiple(array $values, $ttl = null);
    public function deleteMultiple(array $keys);
    public function has(string $key);
}
