<?php

namespace Levinelighto\PhpTester\Commands;

use Levinelighto\PhpTester\Console\BaseCommand;
use Levinelighto\PhpTester\Helpers\MemoryWatcher;
use Levinelighto\PhpTester\Helpers\Stopwatch;

class TestCommand extends BaseCommand
{
    protected string $signature = 'test';

    public function handle()
    {
        MemoryWatcher::start();
        Stopwatch::start();
        try {
        
            // Codes

        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }

        $this->info("Time elapsed: " . Stopwatch::show(true));
        $this->info("Memory used: " . MemoryWatcher::show(true));
    }
}