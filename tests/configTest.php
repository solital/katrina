<?php

# MYSQL
define('DB_CONFIG', [
    'DRIVE' => 'mysql',
    'HOST' => 'localhost',
    'DBNAME' => 'test',
    'USER' => 'root',
    'PASS' => ''
]);

/* define('DB_CACHE', [
    'CACHE_TYPE' => 'memcache',
    'CACHE_HOST' => '127.0.0.1',
    'CACHE_PORT' => 11211,
    'CACHE_TTL' => 600
]); */

# SECOND DATABASE
define('DB_CONFIG_SECONDARY', [
    'HOST' => 'localhost',
    'DBNAME' => 'teste',
    'USER' => 'root',
    'PASS' => ''
]);