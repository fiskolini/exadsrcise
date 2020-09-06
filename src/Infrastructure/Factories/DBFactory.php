<?php

namespace Exadsrcise\Infrastructure\Factories;

use Exadsrcise\Infrastructure\Config;
use Exadsrcise\Infrastructure\DB;

class DBFactory
{
    /**
     * Instantiates a DB instance using .env configuration file
     *
     * @return DB
     */
    public static function factory() : DB
    {
        return new DB(
            Config::get('DB_HOST', 'localhost'),
            Config::get('DB_PORT', 3306),
            Config::get('DB_DATABASE', 'exads'),
            Config::get('DB_USER', 'user'),
            Config::get('DB_PASSWORD'),
        );
    }
}
