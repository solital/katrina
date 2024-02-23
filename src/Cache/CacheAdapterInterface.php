<?php

namespace Katrina\Cache;

interface CacheAdapterInterface
{
    /**
     * @param string $key
     * 
     * @return mixed
     */
    public function get(string $key): mixed;
    
    /**
     * @param string $key
     * @param mixed $data
     * 
     * @return void
     */
    public function set(string $key, mixed $data): void;
    
    /**
     * @param string $key
     * 
     * @return void
     */
    public function delete(string $key): void;
}