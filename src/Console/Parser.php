<?php

namespace Levinelighto\PhpTester\Console;

use InvalidArgumentException;

class Parser
{
    public static function parse(string $expression, array $arguments)
    {
        $output = [
            'arguments' => [],
            'options'   => []
        ];

        if(!preg_match_all('/\{\s*(.*?)\s*\}/', $expression, $matches) || empty($matches)) {
            return $output;
        }

        $args = [];
        $options = [];
        foreach ($arguments as $argument) {
            if (!str_starts_with($argument, '--')) {
                $args[] = $argument;
            } else {
                $optionFragment = explode('=', $argument);
                $key = trim($optionFragment[0], '-');
                $value = !empty($optionFragment[1]) ? $optionFragment[1] : true;

                if (isset($options[$key])) {
                    if (is_array($options[$key])) {
                        $options[$key][] = $value;
                    } else {
                        $options[$key] = [ $options[$key], $value ];
                    }
                } else {
                    $options[$key] = $value;
                }
            }
        }


        $parsedArguments = [];
        $parsedOptions = [];
        foreach ($matches[1] as $token) {
            if (str_starts_with($token, '--')) {
                [$name, $value] = static::parseOption($token, $options);
                $parsedOptions[$name] = $value;
            } else {
                [$name, $value] = static::parseArgument($token, $arguments, count($parsedArguments) - 1);
                $parsedArguments[$name] = $value;
            }
        }

        $output['arguments'] = $parsedArguments;
        $output['options']  = $parsedOptions;
    }


    protected static function parseOption(string $token, array $options)
    {
        $name = trim($token, '-=');
        if (!isset($options[$name])) {
            return [$name, null];
        }

        $value = $options[$name];

        if (!str_ends_with($token, '=')) {
            return [$name, true];
        }

        if (is_bool($value)) {
            throw new InvalidArgumentException("Option {$name} is missing value");
        }

        return [$name, $value];
    }

    protected static function parseArgument(string $token, array $arguments, int $index)
    {
        if (empty($arguments[$index])) {
            throw new InvalidArgumentException("Argument {$token} is missing");
        }

        return [$token, $arguments[$index]];
    }
}