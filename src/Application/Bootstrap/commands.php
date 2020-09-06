<?php
/*
|--------------------------------------------------------------------------
| Load commands list
|--------------------------------------------------------------------------
|
| Application must have many commands in order to work
| properly. We have a list with commands in order to
| be injected to this application
|
*/

use Exadsrcise\Application\Commands;

return [
    Commands\FizzBuzz::class,
    Commands\ElementArray::class,
    Commands\Database::class,
    Commands\Lottery::class,
    Commands\Promotion::class,
];
