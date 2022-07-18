<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Console;

use Illuminate\Console\Command;

/**
 * Class BaseCreateCommand
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Console
 */
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
