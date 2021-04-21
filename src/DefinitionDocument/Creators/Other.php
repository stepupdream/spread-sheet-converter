<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators;

use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\SubAttribute;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports\Facades\SpreadSheetReader;
use Str;

/**
 * Class Other
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators
 */
class Other extends BaseCreator
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
        $this->createDefinitionDocument($attributes, $useBlade, $targetFileName, $outputDirectoryPath);
    }
    
    /**
     * Generate Attribute class based on Sheet data
     *
     * @param  array  $sheet
     * @param  string  $spreadsheetCategoryName
     * @param  int  $rowNumber
     * @param  string  $sheetName
     * @return \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute
     */
    protected function createAttribute(
        array $sheet,
        string $spreadsheetCategoryName,
        int &$rowNumber,
        string $sheetName
    ): Attribute {
        $subAttributes = [];
        $headerNames = $this->sheetOperation->getMainAttributeKeyName($sheet);
        
        $attribute = new Attribute($spreadsheetCategoryName, $sheetName);
        $attribute->setMainKeyName($sheetName);
        while (!empty($sheet[$rowNumber]) && !$this->sheetOperation->isAllEmpty($sheet[$rowNumber])) {
            $subAttribute = new SubAttribute();
            foreach ($headerNames as $headerName) {
                $subAttribute->setAttributes($sheet[$rowNumber][$headerName], $headerName);
            }
            $subAttributes[] = $subAttribute;
            $rowNumber++;
        }
        $attribute->setSubAttributes($subAttributes);
        
        return $attribute;
    }
}
