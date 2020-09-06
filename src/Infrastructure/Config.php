<?php

namespace Exadsrcise\Infrastructure;

use Dotenv\Dotenv;

class Config
{
    /**
     * @var array<string,string|null> $config Configuration
     */
    private static array $config = [];
    /**
     * @var string $dir Directory where .env file is
     */
    private string $dir;

    /**
     * Config constructor.
     *
     * @param string $dir
     */
    public function __construct(string $dir)
    {
        $this->loadConfig(
            $this->dir = $dir
        );
    }

    /**
     * Load config from file
     *
     * @param string $dir
     */
    private function loadConfig(string $dir)
    {
        if( $loadedConfig = Dotenv::createMutable($dir)->load() )
            self::$config = $loadedConfig;
    }

    /**
     * Static factory
     *
     * @param string $dir Directory where .env file is
     *
     * @return Config
     */
    public static function loadFrom(string $dir)
    {
        return new self(...func_get_args());
    }

    /**
     * Get configuration key.
     * Made static only to allow get without constructor injection.
     * However, the best way to do this is using DI Container (see README.md file).
     *
     * @param string $key     Configuration key
     * @param mixed  $default Default value, if required configuration was not found
     *
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return array_key_exists($key, self::$config) ?
            self::$config[$key] : $default;
    }
}
