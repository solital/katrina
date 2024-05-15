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
        if (!extension_loaded('yac')) {
            throw new CacheException('YAC extension not found');
        }

        $this->yac = new \Yac();
        $this->ttl = DB_CACHE['CACHE_TTL'];
    }

    /**
     * @param string $key
     * 
     * @return mixed
     */
    #[\Override]
    public function get(string $key): mixed
    {
        return $this->yac->get($key);
    }

    /**
     * @param string $key
     * 
     * @return bool
     */
    #[\Override]
    public function has(string $key): bool
    {
        $value = $this->get($key);

        if (!empty($value) || $value != false) {
            return true;
        }

        return false;
    }

    /**
     * @param string $key
     * 
     * @return void
     */
    #[\Override]
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
    #[\Override]
    public function save(string $key, mixed $data): mixed
    {
        return $this->yac->set($key, $data, $this->ttl);
    }
}
