<?php

namespace App\Service\Singleton;

use Exception;

class BaseSingleton
{
    private static array $instances = [];

    protected function __construct()
    {
    }

    protected function __clone(): void
    {
    }

    /**
     * @throws Exception
     */
    public function __wakeup(): void
    {
        throw new Exception("Cannot unserialize singleton");
    }

    public static function getInstance(): self
    {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            self::$instances[$subclass] = new static();
        }

        return self::$instances[$subclass];
    }
}
