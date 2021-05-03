<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators;

use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers\SpreadSheetReader;
use Str;

/**
 * Class Base
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators
 */
abstract class Base
{
    /**
     * @var \StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation
     */
    protected $fileOperation;
    
    /**
     * @var \StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers\SpreadSheetReader
     */
    protected $spreadSheetReader;
    
    /**
     * @var string
     */
    protected $categoryName;
    
    /**
     * @var string
     */
    protected $useBladeFileName;
    
    /**
     * @var string
     */
    protected $sheetId;
    
    /**
     * @var string
     */
    protected $outputDirectoryPath;
    
    /**
     * @var string
     */
    protected $separationKey;
    
    /**
     * @var string
     */
    protected $attributeGroupColumnName;
    
    /**
     * BaseCreator constructor.
     *
     * @param  \StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation  $fileOperation
     * @param  \StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers\SpreadSheetReader  $spreadSheetReader
     * @param  array  $readSpreadSheet
     */
    public function __construct(
        FileOperation $fileOperation,
        SpreadSheetReader $spreadSheetReader,
        array $readSpreadSheet
    ) {
        $this->fileOperation = $fileOperation;
        $this->spreadSheetReader = $spreadSheetReader;
        $this->categoryName = $readSpreadSheet['category_name'];
        $this->useBladeFileName = $readSpreadSheet['use_blade'];
        $this->sheetId = $readSpreadSheet['sheet_id'];
        $this->outputDirectoryPath = $readSpreadSheet['output_directory_path'];
        $this->separationKey = $readSpreadSheet['separation_key'];
        $this->attributeGroupColumnName = $readSpreadSheet['attribute_group_column_name'];
    }
    
    /**
     * Execution of processing
     *
     * @param  string|null  $targetFileName
     */
    public function run(string $targetFileName = null): void
    {
        $convertedSheetData = [];
        $spreadSheets = $this->spreadSheetReader->read($this->sheetId);
        foreach ($spreadSheets as $sheetName => $sheet) {
            $convertedSheetData[] = $this->convertSheetData($sheet, Str::studly($this->categoryName), $sheetName);
        }
        
        // Return to one dimension because it is a multi-dimensional array of sheets
        $parentAttributes = collect($convertedSheetData)->flatten()->all();
        $this->verifySheetData($parentAttributes);
        $this->createDefinitionDocument($parentAttributes, $targetFileName);
    }
    
    /**
     * Generate a definition document
     *
     * @param  \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute[]  $parentAttributes
     * @param  string|null  $targetFileName
     */
    public function createDefinitionDocument(array $parentAttributes, ?string $targetFileName): void
    {
        foreach ($parentAttributes as $parentAttribute) {
            // If there is a specification to get only a part, skip other data
            if ($this->isReadSkip($parentAttribute, $targetFileName)) {
                continue;
            }
            $mainKeyName = collect($parentAttribute->parentAttributeDetails())->first();
            $targetPath = $this->outputDirectoryPath.
                DIRECTORY_SEPARATOR.
                Str::studly($parentAttribute->sheetName()).
                DIRECTORY_SEPARATOR.
                $mainKeyName.
                '.yml';
            $loadBladeFile = $this->loadBladeFile($this->useBladeFileName, $parentAttribute);
            $this->fileOperation->createFile($loadBladeFile, $targetPath, true);
        }
    }
    
    /**
     * Read the blade file
     *
     * @param  string  $useBladeFileName
     * @param  \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute  $parentAttribute
     * @return string
     */
    protected function loadBladeFile(string $useBladeFileName, ParentAttribute $parentAttribute): string
    {
        return view('spread_sheet_converter::'.Str::snake($useBladeFileName),
            [
                'parentAttribute' => $parentAttribute,
            ])->render();
    }
    
    /**
     * Whether to skip reading
     *
     * @param  \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute  $parentAttribute
     * @param  string|null  $targetFileName
     * @return bool
     */
    protected function isReadSkip(ParentAttribute $parentAttribute, ?string $targetFileName): bool
    {
        $mainKeyName = collect($parentAttribute->parentAttributeDetails())->first();
        return !empty($targetFileName) && Str::snake($targetFileName) !== Str::snake($mainKeyName);
    }
    
    /**
     * Verification of correct type specification
     *
     * @param  \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute[]  $parentAttributes
     */
    protected function verifySheetData(array $parentAttributes): void
    {
        foreach ($parentAttributes as $parentAttribute) {
            foreach ($parentAttribute->attributesGroup() as $attributes) {
                foreach ($attributes as $attribute) {
                    $this->spreadSheetReader->verifySheetDataDetail($attribute->attributeDetails());
                }
            }
        }
    }
    
    /**
     * Convert spreadsheet data
     *
     * @param  array  $sheet
     * @param  string  $categoryName
     * @param  string  $sheetName
     * @return \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute[]
     */
    protected function convertSheetData(array $sheet, string $categoryName, string $sheetName): array
    {
        $rowNumber = 0;
        $convertedSheetData = [];
        
        while (!empty($sheet[$rowNumber])) {
            if ($this->spreadSheetReader->isAllEmpty($sheet[$rowNumber])) {
                $rowNumber++;
                continue;
            }
            
            $convertedSheetData[] = $this->createParentAttribute($sheet, $categoryName, $rowNumber, $sheetName);
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
     * @return \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute
     */
    protected function createParentAttribute(
        array $sheet,
        string $spreadsheetCategoryName,
        int &$rowNumber,
        string $sheetName
    ): ParentAttribute {
        $headerNamesParent = $this->spreadSheetReader->getParentAttributeKeyName($sheet, $this->separationKey);
        $headerNames = $this->spreadSheetReader->getAttributeKeyName($sheet, $this->separationKey);
        $mainKeyName = collect($headerNames)->first();
        
        $parentAttribute = new ParentAttribute($spreadsheetCategoryName, $sheetName);
        foreach ($headerNamesParent as $headerNameParent) {
            $parentAttribute->setParentAttributeDetails($sheet[$rowNumber][$headerNameParent], $headerNameParent);
        }
        
        while (!empty($sheet[$rowNumber]) && !$this->spreadSheetReader->isAllEmpty($sheet[$rowNumber])) {
            $groupKeyName = $sheet[$rowNumber][$mainKeyName];
            $attributes = $this->createAttributesGroup($sheet, $rowNumber, $headerNames);
            
            if (empty($this->attributeGroupColumnName)) {
                $parentAttribute->setAttributesGroup($attributes);
            } else {
                $parentAttribute->setAttributesGroup($attributes, $groupKeyName);
            }
        }
        
        return $parentAttribute;
    }
    
    /**
     * Create only one attributes group
     *
     * @param  array  $sheet
     * @param  int  $rowNumber
     * @param  array  $headerNames
     * @return array
     */
    protected function createAttributesGroup(array $sheet, int &$rowNumber, array $headerNames): array
    {
        $attributes = [];
        $mainKeyName = collect($headerNames)->first();
        $beforeMainKeyData = $sheet[$rowNumber][$headerNames[$mainKeyName]];
        
        while (true) {
            $attribute = new Attribute();
            foreach ($headerNames as $headerName) {
                $attribute->setAttributeDetails($sheet[$rowNumber][$headerName], $headerName);
            }
            
            if ($this instanceof MultiGroup) {
                $attribute->unsetAttributeDetail($this->attributeGroupColumnName);
                $message = $this->createRuleMessage($sheet, $rowNumber);
                $attribute->setRuleMessage($message);
            }
            
            if (!$this->spreadSheetReader->isAllEmpty($attribute->attributeDetails())) {
                $attributes[] = $attribute;
            }
            $rowNumber++;
            
            if (empty($sheet[$rowNumber]) || $this->spreadSheetReader->isAllEmpty($sheet[$rowNumber])) {
                break;
            }
            
            // If the key of the group is switched, it is judged that the group is finished
            if (!empty($this->attributeGroupColumnName) &&
                $sheet[$rowNumber][$headerNames[$mainKeyName]] !== '' &&
                $sheet[$rowNumber][$headerNames[$mainKeyName]] !== $beforeMainKeyData) {
                break;
            }
        }
        
        return $attributes;
    }
}
