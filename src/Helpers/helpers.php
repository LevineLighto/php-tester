<?php

use Levinelighto\PhpTester\Console\Console;

if (!function_exists('base_path')) {
    function base_path(?string $path = '')
    {
        return Console::get()?->basePath($path);
    }
}

if (!function_exists('consoleInfo')) {
    function consoleInfo(string $message) 
    {
        echo "\033[32m{$message}\033[0m\n";
    }
}

if (!function_exists('consoleError')) {
    function consoleError(string $message) 
    {
        echo "\033[31m{$message}\033[0m\n";
    }
}

