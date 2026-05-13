<?php

namespace Levinelighto\PhpTester\Console;

use InvalidArgumentException;
use Levinelighto\PhpTester\Helpers\ClassFinder;
use Levinelighto\PhpTester\Log\Log;

class Console
{
    private static Console $entity;
    private static array $commands = [];

    private function __construct(private string $baseDir, private ?string $commandNamespace = 'Levinelighto\\PhpTester\\Commands')
    {
    }

    // Static functions

    public static function make(string $baseDir)
    {
        try {
            if (isset(static::$entity)) {
                return static::$entity;
            }
    
            $entity = new static($baseDir);
            static::$entity = $entity;
    
            $entity->registerCommand();
    
            return $entity;
        } catch (\Throwable $th) {
            consoleError($th);
            Log::error($th);
        }
    }

    public static function get() : Console|null
    {
        if (isset(static::$entity)) {
            return static::$entity;
        }

        return null;
    }

    
    // Non-static functions

    public function basePath(?string $path = '')
    {
        if (!$path) {
            return $this->baseDir;
        }

        return $this->baseDir . '/' . $path;
    }

    public function handle()
    {
        try {
            $argv = $_SERVER['argv'];
            
            array_shift($argv);
    
            $commandName = array_shift($argv);
            if (!$commandName) {
                throw new InvalidArgumentException("Missing command name");
            }
    
            $command = $this->findCommand($commandName);
            if (empty($command)) {
                throw new InvalidArgumentException("Unable to find command {$commandName}");
            }
    
            $command->execute($argv);
        } catch (\Throwable $th) {
            $message = $th->getMessage();

            consoleError($message);
            Log::error($th);
        }
    }


    // Private functions

    private function registerCommand()
    {
        if (!empty(static::$commands)) {
            return;
        }

        $classes = ClassFinder::getInNamespace($this->commandNamespace);
        foreach ($classes as $class) {
            $entity = new $class;
            if (!($entity instanceof BaseCommand)) {
                continue;
            }

            $commandName = $entity->getCommandName();
            if (!$commandName) {
                continue;
            }

            static::$commands[$commandName] = $entity;
        }
    }

    private function findCommand(string $commandName) : BaseCommand
    {
        if (!isset(static::$commands[$commandName])) {
            throw new InvalidArgumentException("Unable to find {$commandName}");
        }

        return static::$commands[$commandName];
    }
}