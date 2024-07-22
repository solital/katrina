<?php

namespace Katrina\Cache;

use Katrina\Exceptions\CacheException;

class MemcachedAdapter implements CacheAdapterInterface
{
    /**
     * @var int
     */
    private int $ttl = 300;

    /**
     * @var Memcached
     */
    private ?\Memcached $cache;

    public function __construct()
    {
        $this->connection(DB_CACHE['CACHE_HOST'], DB_CACHE['CACHE_PORT']);
    }

    /**
     * @param string $host
     * @param int $port
     * 
     * @return MemcachedAdapter
     * @throws CacheException
     */
    public function connection(string $host, int $port): MemcachedAdapter
    {
        if (!extension_loaded('Memcached')) throw new CacheException("Extension Memcached not found");
        if (isset(DB_CACHE['CACHE_TTL'])) $this->ttl = DB_CACHE['CACHE_TTL'];

        $this->cache = new \Memcached();
        $this->cache->addServer($host, $port);

        if ($this->cache->getStats() == false) throw new CacheException("Not connected to memcached server");
        return $this;
    }

    /**
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return $this->cache->get($key);
    }

    /**
     * @param mixed $data
     * 
     * @return void
     */
    public function set(string $key, mixed $data): void
    {
        $this->cache->set($key, $data, $this->ttl);
    }

    /**
     * @param string $key
     * 
     * @return void
     */
    public function delete(string $key): void
    {
        $this->cache->delete($key);
    }
}
