<?php

namespace StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports\Facades;

use Illuminate\Support\Facades\Facade;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Reader\BaseReader;

/**
 * Class SpreadSheetReader
 *
 * @package StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports\Facades
 */
class SpreadSheetReader extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BaseReader::class;
    }
}
