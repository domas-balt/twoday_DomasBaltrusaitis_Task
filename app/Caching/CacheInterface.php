<?php
declare(strict_types=1);

namespace App\Caching;

interface CacheInterface
{
    public function get(int|string|float $key, $default = null);
    public function set(int|string|float  $key, int|string|float  $value, $ttl = null);
    public function delete(int|string|float  $key);
    public function clear();
    public function getMultiple(array $keys, $default = null);
    public function setMultiple(array $values, $ttl = null);
    public function deleteMultiple(array $keys);
    public function has(int|string|float  $key);
}