<?php

namespace Levinelighto\PhpTester\Helpers;

use Exception;

/**
 * Singleton helper to keep track of how much
 * memory has been used since the watcher started.
 */
class MemoryWatcher
{
    private static MemoryWatcher|null $instance = null;

    const UNITS     = [' bytes', ' kB', ' MB', ' GB'];
    const DIVIDERS  = [1, 1024, 1024, 1024];
    
    public $memoryStart;

    private function __construct()
    {
        $this->memoryStart = memory_get_usage();
    }

    /**
     * Start watcher
     */
    public static function start()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }
    }

    /**
     * Stop watcher
     */
    public static function stop()
    {
        if (empty(static::$instance)) {
            return;
        }

        $instance = static::$instance;
        static::$instance = null;

        unset($instance);
    }

    /**
     * Shows how much memory has been used since it started
     * 
     * @param ?bool $stop 
     * Stop keeping track of memory after the value is displayed
     */
    public static function show(?bool $stop = false)
    {
        if (empty(static::$instance)) {
            throw new Exception("Watcher is not started");
        }

        $after = memory_get_usage();
        
        $result = $after - (static::$instance)->memoryStart;

        $results = [];

        foreach (static::UNITS as $index => $unit) {
            $result     = floor($result / static::DIVIDERS[$index]);

            if ($index == count(static::UNITS) - 1) {
                $results = [ "{$result}{$unit}", ...$results ];
                break;
            }

            if ($result <= static::DIVIDERS[$index + 1]) {
                $results = [ "{$result}{$unit}", ...$results ];
                break;
            }

            $remainder  = $result % static::DIVIDERS[$index + 1];

            if ($remainder) {
                $results = [ "{$remainder}{$unit}", ...$results ];
            }

            if (count($results) >= 3) {
                array_pop($results);
            }

            if (!$result) {
                break;
            }
        }

        if (count($results) >= 3) {
            array_pop($results);
        }

        if ($stop) {
            static::stop();
        }

        return implode(' ', $results);
    }
}