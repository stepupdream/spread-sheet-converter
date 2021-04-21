<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators;

use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\SubAttribute;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports\Facades\SpreadSheetReader;
use Str;

/**
 * Class Table
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators
 */
class Table extends BaseCreator
{
    /**
     * Execution of processing
     *
     * @param  string  $categoryName
     * @param  string  $useBlade
     * @param  string  $sheetId
     * @param  string  $outputDirectoryPath
     * @param  string|null  $targetFileName
     */
    public function run(
        string $categoryName,
        string $useBlade,
        string $sheetId,
        string $outputDirectoryPath,
        string $targetFileName = null
    ): void {
        $convertedSheetData = [];
        $spreadSheets = SpreadSheetReader::read($sheetId);
        
        foreach ($spreadSheets as $sheetName => $sheet) {
            $convertedSheetData[] = $this->convertSheetData($sheet, Str::studly($categoryName), $sheetName);
        }
        
        // Return to one dimension because it is a multi-dimensional array of sheets
        $attributes = collect($convertedSheetData)->flatten()->all();
        $this->verifyDataTypeTable($attributes);
        $this->createDefinitionDocument($attributes, $useBlade, $targetFileName, $outputDirectoryPath);
    }
}
