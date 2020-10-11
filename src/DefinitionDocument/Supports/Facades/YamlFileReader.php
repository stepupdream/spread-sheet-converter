<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\Facades;

use Illuminate\Support\Facades\Facade;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Reader\BaseReader;

/**
 * Class YamlFileReader
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\Facades
 */
class YamlFileReader extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() : string
    {
        return BaseReader::class;
    }
}
