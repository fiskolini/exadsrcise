<?php

namespace Exadsrcise\Persistency\Repositories;

use Exadsrcise\Infrastructure\DB;
use Exadsrcise\Infrastructure\Exceptions\DBException;

class TestRepository
{

    /**
     * Fetch all records.
     * Could provide a callable instead keep it into a variable and iterate manually
     *
     * @return array
     * @throws DBException
     */
    public static function fetchAll(callable $callable = null)
    {
        return DB::instance()->selectFrom("exads_test")->all($callable);
    }

    /**
     * Insert new record in Test
     *
     * @param string $name
     * @param int    $age
     * @param string $job
     *
     * @throws DBException
     */
    public static function insert(string $name, int $age, string $job)
    {
        DB::instance()->insertRow('exads_test', ['name', 'age', 'job_title'], func_get_args());
    }
}
