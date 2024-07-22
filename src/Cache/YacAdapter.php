<?php

namespace Katrina\Cache;

use Katrina\Exceptions\CacheException;

class YacAdapter implements CacheAdapterInterface
{
    /**
     * @var int
     */
    private int $ttl = 600;

    /**
     * @var mixed
     */
    private mixed $yac;

    public function __construct()
    {
        if (!extension_loaded('yac')) throw new CacheException('YAC extension not found');

        $this->yac = new \Yac();
        $this->ttl = DB_CACHE['CACHE_TTL'];
    }

    /**
     * @param string $key
     * 
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return $this->yac->get($key);
    }

    /**
     * @param mixed $data
     * 
     * @return void
     */
    public function set(string $key, mixed $data): void
    {
        $this->yac->set($key, $data, $this->ttl);
    }

    /**
     * @param string $key
     * 
     * @return bool
     */
    public function has(string $key): bool
    {
        $value = $this->get($key);
        if (!empty($value) || $value != false) return true;
        return false;
    }

    /**
     * @param string $key
     * 
     * @return void
     */
    public function delete(string $key): void
    {
        $this->yac->delete($key);
    }

    /**
     * @param string $key
     * @param mixed $data
     * 
     * @return mixed
     */
    public function save(string $key, mixed $data): mixed
    {
        return $this->yac->set($key, $data, $this->ttl);
    }
}
