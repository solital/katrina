<?php

namespace Katrina\Cache;

use Katrina\Exceptions\CacheException;

class APCuAdapter implements CacheAdapterInterface
{
    /**
     * @var int
     */
    private int $ttl;

    public function __construct()
    {
        if (!extension_loaded('apcu')) throw new CacheException("Extension Memcache not found");
        if (!apcu_enabled()) throw new CacheException('Not connected to apcu cache');

        $this->ttl = DB_CACHE['CACHE_TTL'];
    }

    /**
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return apcu_fetch($key);
    }

    /**
     * @param mixed $data
     * 
     * @return void
     */
    public function set(string $key, mixed $data): void
    {
        apcu_store($key, $data, $this->ttl);
    }

    /**
     * @param string $key
     * 
     * @return void
     */
    public function delete(string $key): void
    {
        apcu_delete($key);
    }
}
