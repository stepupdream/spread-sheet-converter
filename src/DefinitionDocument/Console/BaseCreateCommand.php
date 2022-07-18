<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Console;

use Illuminate\Console\Command;

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
}
