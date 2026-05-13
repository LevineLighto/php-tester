<?php

namespace Levinelighto\PhpTester\Console;

use InvalidArgumentException;

abstract class BaseCommand
{
    protected string $signature;
    protected array $arguments = [];
    protected array $options = [];

    abstract public function handle();

    public function getCommandName()
    {
        if (empty($this->signature)) {
            return null;
        }

        if (!preg_match('/[^\s]+/', $this->signature, $matches)) {
            throw new InvalidArgumentException('Unable to determine command name');
        }

        return $matches[0];
    }

    public function execute($arguments)
    {
        $parsed = Parser::parse($this->signature, $arguments);

        $this->arguments = $parsed['arguments'];
        $this->options = $parsed['options'];

        $this->handle();
    }


    protected function argument(string $name, mixed $default = null)
    {
        if (isset($this->arguments[$name])) {
            return $this->arguments[$name];
        }

        return $default;
    }

    protected function option(string $name, mixed $default = null)
    {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }

        return $default;
    }

    protected function info(string $message)
    {
        consoleInfo($message);
    }

    protected function error(string $message)
    {
        consoleError($message);
    }
}