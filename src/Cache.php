<?php

namespace Katrina;

use Katrina\Exceptions\CacheException;
use Memcache;

class Cache
{
    /**
     * @var Memcache|null|
     */
    private ?Memcache $cache = null;

    public function __construct()
    {
        if (defined('DB_CACHE')) {
            if (class_exists('Memcache')) {
                $this->cache = new Memcache();
                $this->cache->addServer(DB_CACHE['CACHE_HOST'], DB_CACHE['CACHE_PORT']);
            } else {
                throw new CacheException("Not connected to cache server");
            }
        }
    }

    /**
     * @return mixed
     */
    public function get(string $key): mixed
    {
        if ($this->cache != null) {
            return $this->cache->get($key);
        } else {
            return null;
        }
    }

    /**
     * @param mixed $data
     * 
     * @return void
     */
    public function set(string $key, mixed $data): void
    {
        if ($this->cache != null) {
            $this->cache->set($key, $data);
        }
    }

    /**
     * @param string $key
     * 
     * @return void
     */
    public function delete(string $key): void
    {
        if ($this->cache != null) {
            $this->cache->delete($key);
        }
    }
}
