<?php

namespace App\Interfaces;

interface ICache
{
    public function get($key, $default = null);
    public function set($key, $value, $ttl = null);
    public function delete($key);
    public function clear();
    public function getMultiple(array $keys, $default = null);
    public function setMultiple(array $values, $ttl = null);
    public function deleteMultiple(array $keys);
    public function has($key);
}