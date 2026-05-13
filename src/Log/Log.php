<?php

namespace Levinelighto\PhpTester\Log;

use FilesystemIterator;
use Levinelighto\PhpTester\Storage\Storage;

class Log
{
    public static function info (mixed $message)
    {
        static::write("Info: {$message}");
    }

    public static function debug (mixed $message)
    {
        static::write("Debug: {$message}");
    }

    public static function error (mixed $message)
    {
        static::write("Error: {$message}");
    }


    protected static function write (mixed $message)
    {
        $fileName = "tester-log-" . date('Y-m-d') . ".log";
        $time = date('Y-m-d H:i:s');
        $path = "logs/{$fileName}";

        if (!Storage::exists($path)) {
            static::pruneLogs();
        }


        $content = "[$time] {$message}\n";
        Storage::update($path, $content);
    }

    protected static function pruneLogs()
    {
        $iterator = new FilesystemIterator(Storage::path('logs'));
        
        $fileNames = [];
        foreach ($iterator as $file) {
            $extension = $file->getExtension();
            if ($extension != 'log') {
                continue;
            }

            $fileNames[] = $file->getFilename();
        }

        sort($fileNames);

        $counter = count($fileNames);
        $exceeding = $counter - 13;
        if ($exceeding <= 0) {
            return;
        }

        for ($i = 0; $i < $exceeding; $i++) { 
            $fileName = array_shift($fileNames);

            Storage::delete("logs/{$fileName}");
        }
    }
}