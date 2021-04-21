<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators;

use LogicException;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\SubAttribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports\Facades\SpreadSheetReader;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports\SheetOperation;
use Str;

/**
 * Class BaseCreator
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators
 */
abstract class BaseCreator
{
    /**
     * @var \StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports\SheetOperation
     */
    protected $sheetOperation;
    
    /**
     * @var \StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation
     */
    protected $fileOperation;
    
    /**
     * BaseCreator constructor.
     *
     * @param  \StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation  $fileOperation
     * @param  \StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports\SheetOperation  $sheetOperation
     */
    public function __construct(
        FileOperation $fileOperation,
        SheetOperation $sheetOperation
    ) {
        $this->sheetOperation = $sheetOperation;
        $this->fileOperation = $fileOperation;
    }
    
    /**
     * Generate a definition document
     *
     * @param  \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute[]  $attributes
     * @param  string  $useBladeFileName
     * @param  string|null  $targetFileName
     * @param  string  $outputDirectoryPath
     */
    public function createDefinitionDocument(
        array $attributes,
        string $useBladeFileName,
        ?string $targetFileName,
        string $outputDirectoryPath
    ): void {
        foreach ($attributes as $attribute) {
            // If there is a specification to get only a part, skip other data
            if ($this->isReadSkip($attribute, $targetFileName)) {
                continue;
            }
            
            $targetPath = $outputDirectoryPath.DIRECTORY_SEPARATOR.Str::studly($attribute->sheetName()).DIRECTORY_SEPARATOR.$attribute->mainKeyName().'.yml';
            $loadBladeFile = $this->loadBladeFile($useBladeFileName, $attribute);
            
            $this->fileOperation->createFile($loadBladeFile, $targetPath, true);
        }
    }
    
    /**
     * Read the blade file
     *
     * @param  string  $useBladeFileName
     * @param  mixed  $attribute
     * @return string
     */
    protected function loadBladeFile(string $useBladeFileName, $attribute): string
    {
        return view('definition_document::'.Str::snake($useBladeFileName),
            [
                'attribute' => $attribute,
            ])->render();
    }
    
    /**
     * Whether to skip reading
     *
     * @param  \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute|\StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ApiAttribute  $attribute
     * @param  string|null  $targetFileName
     * @return bool
     */
    protected function isReadSkip($attribute, ?string $targetFileName): bool
    {
        return $targetFileName !== null && Str::snake($targetFileName) !== Str::snake($attribute->mainKeyName());
    }
    
    /**
     * Verification of correct type specification
     *
     * @param  \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute[]  $attributes
     */
    protected function verifyDataTypeTable(array $attributes): void
    {
        foreach ($attributes as $attribute) {
            foreach ($attribute->subAttributes() as $subAttribute) {
                if (isset($subAttribute->attributes()['ColumnName'], $subAttribute->attributes()['DataType'])) {
                    SpreadSheetReader::verifyDataTypeDetail($subAttribute->attributes()['ColumnName'], $subAttribute->attributes()['DataType']);
                }
            }
        }
    }
    
    /**
     * Verification of correct type specification
     *
     * @param  \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ApiAttribute[]  $attributes
     */
    protected function verifyDataTypeForHttp(array $attributes): void
    {
        foreach ($attributes as $attribute) {
            foreach ($attribute->requestAttributes() as $subAttribute) {
                SpreadSheetReader::verifyDataTypeDetail($subAttribute->attributes()['ColumnName'], $subAttribute->attributes()['DataType']);
            }
            foreach ($attribute->responseAttributes() as $subAttribute) {
                SpreadSheetReader::verifyDataTypeDetail($subAttribute->attributes()['ColumnName'], $subAttribute->attributes()['DataType']);
            }
        }
    }
    
    /**
     * Validate header name is correct
     *
     * @param  array  $headerNamesSub
     */
    protected function verifyHeaderName(array $headerNamesSub): void
    {
        if (empty($headerNamesSub['ColumnName']) || empty($headerNamesSub['DataType'])) {
            throw new LogicException('ColumnType and ColumnName data could not be read');
        }
    }
    
    /**
     * Validate header name is correct
     *
     * @param  array  $headerNamesSub
     */
    protected function verifyHeaderNameForHttp(array $headerNamesSub): void
    {
        if (empty($headerNamesSub['ColumnType'] || empty($headerNamesSub['ColumnName']) || empty($headerNamesSub['DataType']))) {
            throw new LogicException('ColumnType and ColumnName and DataType data could not be read');
        }
    }

    /**
     * Convert spreadsheet data
     *
     * @param  array  $sheet
     * @param  string  $categoryName
     * @param  string  $sheetName
     * @return \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute[]
     */
    protected function convertSheetData(array $sheet, string $categoryName, string $sheetName): array
    {
        $rowNumber = 0;
        $convertedSheetData = [];
        
        while (!empty($sheet[$rowNumber])) {
            if ($this->sheetOperation->isAllEmpty($sheet[$rowNumber])) {
                $rowNumber++;
                continue;
            }
            
            $convertedSheetData[] = $this->createAttribute($sheet, $categoryName, $rowNumber, $sheetName);
        }
        
        return $convertedSheetData;
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
        $headerNamesMain = $this->sheetOperation->getMainAttributeKeyName($sheet);
        $headerNamesSub = $this->sheetOperation->getSubAttributeKeyName($sheet);
        $this->verifyHeaderName($headerNamesSub);
        
        $subAttributes = [];
        
        $attribute = new Attribute($spreadsheetCategoryName, $sheetName);
        $attribute->setMainKeyName(collect($sheet[$rowNumber])->first());
        foreach ($headerNamesMain as $headerNameMain) {
            $attribute->setAttributes($sheet[$rowNumber][$headerNameMain], $headerNameMain);
        }
        
        while (!empty($sheet[$rowNumber]) && !$this->sheetOperation->isAllEmpty($sheet[$rowNumber])) {
            $subAttribute = new SubAttribute();
            $subAttribute->setMainKeyName($sheet[$rowNumber][$headerNamesSub['ColumnName']]);
            foreach ($headerNamesSub as $headerNameSub) {
                $subAttribute->setAttributes($sheet[$rowNumber][$headerNameSub], $headerNameSub);
            }
            $subAttributes[] = $subAttribute;
            $rowNumber++;
        }
        $attribute->setSubAttributes($subAttributes);
        
        return $attribute;
    }
}
