<?php

namespace Levinelighto\PhpTester\Storage;

class Storage
{
    public static function read(string $path)
    {
        if (!static::exists($path)) {
            return null;
        }

        return file_get_contents(static::path($path));
    }

    public static function create(string $path, mixed $data)
    {
        if (!static::directoryExists(dirname($path))) {
            static::createDirectory(dirname($path));       
        }

        $path = static::path($path);
        file_put_contents($path, $data);
    }

    public static function update(string $path, mixed $data)
    {
        if (!static::directoryExists(dirname($path))) {
            static::createDirectory(dirname($path));       
        }

        $path = static::path($path);
        file_put_contents($path, $data, FILE_APPEND);
    }

    public static function delete(string $path)
    {
        if (!static::exists($path)) {
            return;
        }
        
        $path = static::path($path);
        if (!is_file($path)) {
            return;
        }

        unlink($path);
    }

    public static function createDirectory(string $path)
    {
        if (static::directoryExists($path)) {
            return;
        }

        $path = static::path($path);
        mkdir($path, 0777, true);
    }

    public static function exists(string $path)
    {
        return file_exists(static::path($path));
    }

    public static function directoryExists(string $path)
    {
        $path = static::path($path);
        return file_exists($path) && is_dir($path);
    }

    public static function path(string $path)
    {
        return base_path("storage/app/{$path}");
    }
}