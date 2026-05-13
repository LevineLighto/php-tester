<?php

namespace Levinelighto\PhpTester\Helpers;

use Exception;

/**
 * Singleton helper to keep track of how long
 * time has passed since the stopwatch started.
 */
class Stopwatch
{
    private static Stopwatch|null $instance = null;

    const UNITS     = ['ns', 'us', 'ms', 's', ' min'];
    const DIVIDERS  = [1, 1e3, 1e3, 1e3, 60];
    
    public $timeStart;

    private function __construct()
    {
        $this->timeStart = hrtime(true);
    }

    /**
     * Start stopwatch
     */
    public static function start()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }
    }

    /**
     * Stop stopwatch
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
     * Shows how much time has passed since it started
     * 
     * @param ?bool $stop 
     * Stop keeping track of time after the value is displayed
     */
    public static function show(?bool $stop = false)
    {
        if (empty(static::$instance)) {
            throw new Exception("Stopwatch is not started");
        }

        $after = hrtime(true);
        
        $result = $after - (static::$instance)->timeStart;

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