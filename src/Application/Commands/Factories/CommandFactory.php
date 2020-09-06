<?php

namespace Exadsrcise\Application\Commands\Factories;

use Exadsrcise\Application\Contracts\CommandInterface;
use Exadsrcise\Application\Exceptions\InvalidCommandException;
use \ReflectionClass;
use \ReflectionException;

class CommandFactory
{
    /**
     * Builds command instance
     *
     * @param string $namespace namespace to build
     *
     * @return object|CommandInterface
     * @throws ReflectionException
     */
    public static function factory(string $namespace) : object
    {
        if(self::isValid($class = new ReflectionClass($namespace)))
            return $class->newInstance();

        throw new InvalidCommandException(
            sprintf('Invalid command %s', $namespace)
        );
    }

    /**
     * Check if given class is valid to factory
     *
     * @param ReflectionClass $class Class instance to test
     *
     * @return bool
     */
    private static function isValid(ReflectionClass $class)
    {
        return $class->implementsInterface(CommandInterface::class) && $class->isInstantiable();
    }
}
