<?php

namespace App\Caching;

interface CacheInterface
{
    public function get(mixed $key, $default = null);
    public function set(mixed $key, mixed $value, $ttl = null);
    public function delete(mixed $key);
    public function clear();
    public function getMultiple(array $keys, $default = null);
    public function setMultiple(array $values, $ttl = null);
    public function deleteMultiple(array $keys);
    public function has(mixed $key);
}