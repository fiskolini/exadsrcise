<?php

namespace Exadsrcise\Application;

use Exadsrcise\Infrastructure\Config;
use Exception;
use Exadsrcise\Application\Exceptions\InvalidCommandException;
use Exadsrcise\Application\Contracts\CommandInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

final class ExadsApp
{
    /**
     * @var Application
     */
    private Application $application;

    /**
     * @var string $root Root path of application
     */
    private string $root;

    /**
     * ExadsApp constructor.
     *
     * @param string $root Root directory of application
     */
    public function __construct(string $root)
    {
        $this->application = new Application();
        $this->root = $root;
    }

    /**
     * Run application
     *
     * @throws Exception
     */
    public function run() : void
    {
        $this->loadDependencies();

        $this->application->run();
    }

    /**
     * Add command
     *
     * @param CommandInterface $command
     */
    public function addCommand(CommandInterface $command)
    {
        if($command instanceof Command){
            $this->application->add($command);
            return;
        }

        throw new InvalidCommandException(
            sprintf('Invalid command %s', get_class($command))
        );
    }

    /**
     * Load application dependencies
     * NOTE: This should be done using DI Container (see README.md file).
     */
    private function loadDependencies()
    {
        // Configuration dependency
        Config::loadFrom($this->root);
    }
}
