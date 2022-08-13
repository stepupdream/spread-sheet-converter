<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Console;

use Illuminate\Console\Command;
use Illuminate\Console\View\Components\Info;

abstract class BaseCreateCommand extends Command
{
    /**
     * Create a new console command instance.
     */
    public function __construct()
    {
        ini_set('memory_limit', '2056M');

        parent::__construct();
    }

    /**
     * Command execution log.
     *
     * @return void
     */
    public function commandDetailLog(): void
    {
        (new Info($this->output))->render('Command run detail');
        $runTime = number_format((microtime(true) - LARAVEL_START) * 1000).'ms';
        $usedMemory = sprintf('%sMB', memory_get_peak_usage(true) / 1024 / 1024);
        $this->line(sprintf("  run_time : %s\n  used_memory : %s\n", $runTime, $usedMemory));
    }
}
