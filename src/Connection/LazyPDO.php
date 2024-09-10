<?php

namespace Katrina\Connection;

use PDO;
use PDOException;
use PDOStatement;
use SensitiveParameter;

class LazyPDO extends PDO
{
    /** 
     * @var PDO
     */
    private ?PDO $pdo_conn = null;

    /**
     * @var string
     */
    private string $dsn;

    /**
     * @var string|null
     */
    private ?string $username;

    /**
     * @var string|null
     */
    private ?string $password;

    /**
     * @var array
     */
    private ?array $options;

    /**
     * @var mixed|null
     */
    private mixed $onConnectionErrorCallback = null;

    /**
     * @var mixed|null
     */
    private mixed $onConnectCallback = null;

    /**
     * @var mixed|null
     */
    private mixed $onCloseCallback = null;

    /**
     * {@inheritDoc}
     */
    public function __construct(
        string $dsn,
        ?string $user = null,
        #[SensitiveParameter] ?string $password = null,
        ?array $options = null
    ) {
        $this->pdo_conn = null;
        $this->dsn = $dsn;
        $this->username = $user;
        $this->password  = $password;
        $this->options = $options;

        /** @var callable onConnectionErrorCallback */
        $this->onConnectionErrorCallback = function ($e) {
            // By default, bubble up the exception
            throw $e;
        };

        $this->onConnectCallback = function () {};
        /** @var callable onCloseCallback */
        $this->onCloseCallback   = function () {};

        register_shutdown_function([$this, 'close']);
    }

    /**
     * Sets a function to be called if there is an error when trying to establish a connection with the underlying PDO object
     * 
     * @param callable $callback
     */
    public function onConnectionError(callable $callback): void
    {
        $this->onConnectionErrorCallback = $callback;
    }

    /**
     * Sets a function to be called after the underlying PDO object succesfully establishes a connection
     * 
     * @param callable $callback
     */
    public function onConnectionOpen(callable $callback): void
    {
        $this->onConnectCallback = $callback;
    }

    /**
     * Sets a function to be called after the underlying PDO object is set to null, closing the connection.
     * 
     * @param callable $callback
     */
    public function onConnectionClose(callable $callback): void
    {
        $this->onCloseCallback = $callback;
    }

    /**
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->pdo_conn !== null;
    }

    /**
     * Connect, if the connection has not been established already
     */
    public function getConnecttion()
    {
        if (!$this->isConnected()) {
            try {
                $this->pdo_conn = new parent($this->dsn, $this->username, $this->password, $this->options);
                $callback = $this->onConnectCallback;
                $callback($this->pdo_conn);
            } catch (PDOException $e) {
                $callback = $this->onConnectionErrorCallback;
                $callback($e);
            }
        }
    }

    /**
     * Close the connection
     */
    public function close()
    {
        if ($this->isConnected()) {
            $this->pdo_conn = null;
            $callback = $this->onCloseCallback;
            $callback();
        }
    }

    /* Override parent functions to make them lazy */

    /**
     * {@inheritDoc}
     */
    public function beginTransaction(): bool
    {
        $this->getConnecttion();
        return $this->pdo_conn->beginTransaction();
    }

    /**
     * {@inheritDoc}
     */
    public function commit(): bool
    {
        $this->getConnecttion();
        return $this->pdo_conn->commit();
    }

    /**
     * {@inheritDoc}
     */
    public function rollBack(): bool
    {
        $this->getConnecttion();
        return $this->pdo_conn->rollBack();
    }

    /**
     * {@inheritDoc}
     */
    public function inTransaction(): bool
    {
        $this->getConnecttion();
        return $this->pdo_conn->inTransaction();
    }

    /**
     *{@inheritDoc}
     */
    public function errorCode(): ?string
    {
        $this->getConnecttion();
        return $this->pdo_conn->errorCode();
    }

    /**
     * {@inheritDoc}
     */
    public function errorInfo(): array
    {
        $this->getConnecttion();
        return $this->pdo_conn->errorInfo();
    }

    /**
     * {@inheritDoc}
     */
    public function exec(string $statement): int|false
    {
        $this->getConnecttion();
        return $this->pdo_conn->exec($statement);
    }

    /**
     * {@inheritDoc}
     */
    public function getAttribute(int $attribute): mixed
    {
        $this->getConnecttion();
        return $this->pdo_conn->getAttribute($attribute);
    }

    /**
     * {@inheritDoc}
     */
    public function setAttribute(int $attribute, mixed $value): bool
    {
        $this->getConnecttion();
        return $this->pdo_conn->setAttribute($attribute, $value);
    }

    /**
     * {@inheritDoc}
     */
    public static function getAvailableDrivers(): array
    {
        return parent::getAvailableDrivers();
    }

    /**
     * {@inheritDoc}
     */
    public function lastInsertId(?string $name = null): string|false
    {
        $this->getConnecttion();
        return $this->pdo_conn->lastInsertId($name);
    }

    /**
     * {@inheritDoc}
     */
    public function prepare(string $statement, array $options = []): PDOStatement|false
    {
        $this->getConnecttion();
        if (!is_array($options)) $options = [];
        return $this->pdo_conn->prepare($statement, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function query($query, $fetchMode = null, ...$fetchModeArgs): PDOStatement|false
    {
        /* I don't use the arguments as passed directly, but instead I get the arguments as an
        array and unpack it. This is because there seems to be several implementations of the
        query() method in native PDO, and calling it with the arguments as passed to this function
        almost always returns an error. This fixes it. */
        $this->getConnecttion();
        $args = func_get_args();
        return $this->pdo_conn->query(...$args);
    }

    /**
     * {@inheritDoc}
     */
    public function quote(string $string, int $parameter_type = parent::PARAM_STR): string|false
    {
        $this->getConnecttion();
        return $this->pdo_conn->quote($string, $parameter_type);
    }
}
