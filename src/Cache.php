<?php

namespace Katrina;

use Katrina\Cache\{APCuAdapter, MemcacheAdapter, MemcachedAdapter, CacheAdapterInterface, YacAdapter};
use Katrina\Exceptions\CacheException;

class Cache implements CacheAdapterInterface
{
    /**
     * @var CacheAdapterInterface
     */
    private ?CacheAdapterInterface $cache = null;

    /**
     * @param string|null $cache_status
     */
    public function __construct(?string $cache_status)
    {
        if ($cache_status != null || $cache_status == true) {
            if (defined('DB_CACHE')) {
                switch (DB_CACHE['CACHE_TYPE']) {
                    case 'memcache':
                        $this->cache = new MemcacheAdapter();
                        break;

                    case 'memcached':
                        $this->cache = new MemcachedAdapter();
                        break;

                    case 'apcu':
                        $this->cache = new APCuAdapter();
                        break;

                    case 'yac':
                        $this->cache = new YacAdapter();
                        break;

                    default:
                        throw new CacheException('Cache driver not found');
                        break;
                }
            } else {
                throw new CacheException("Katrina Cache error: Check your 'cache.yaml' file or constants for cache config");
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
        }

        return null;
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
