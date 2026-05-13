<?php

namespace Levinelighto\PhpTester\Helpers;

class ClassFinder
{
    public static function getInNamespace(string $namespace)
    {
        $files = scandir(static::getNamespaceDirectory($namespace));

        $classes = [];
        foreach ($files as $filename) {
            $class = $namespace . '\\' . str_replace('.php', '', $filename);
            if (class_exists($class)) {
                $classes[] = $class;
            }
        }

        return $classes;
    }


    // Private functions

    private static function getDefinedNamespaces()
    {
        $path = base_path('composer.json');
        $composer = json_decode(file_get_contents($path), true);

        return $composer['autoload']['psr-4'];
    }

    private static function getNamespaceDirectory(string $namespace)
    {
        $namespaces = static::getDefinedNamespaces();

        $inputtedNamespaceFragments = explode('\\', $namespace);
        $namespaceFragments = [];
        
        while($inputtedNamespaceFragments) {
            $namespaceFragments[] = array_shift($inputtedNamespaceFragments);
            $possibleNamespace = implode('\\', $namespaceFragments) . '\\';

            if (!array_key_exists($possibleNamespace, $namespaces)) {
                continue;
            }

            $path = $namespaces[$possibleNamespace];
            return realpath(base_path($path . implode('/', $inputtedNamespaceFragments)));
        }
    }
}