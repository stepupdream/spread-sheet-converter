<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions;

/**
 * Class Attribute
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions
 */
class Attribute
{
    /**
     * @var string
     */
    protected $spreadsheetCategoryName;
    
    /**
     * @var array
     */
    protected $attributes = [];
    
    /**
     * @var string
     */
    protected $sheetName;
    
    /**
     * @var string
     */
    protected $mainKeyName = '';
    
    /**
     * @var \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\SubAttribute[]
     */
    protected $subAttributes = [];
    
    /**
     * Attribute constructor.
     *
     * @param  string  $spreadsheetCategoryName
     * @param  string  $sheetName
     */
    public function __construct(
        string $spreadsheetCategoryName,
        string $sheetName
    ) {
        $this->spreadsheetCategoryName = $spreadsheetCategoryName;
        $this->sheetName = $sheetName;
    }
    
    /**
     * get spreadsheet tile name
     *
     * @return string
     */
    public function spreadsheetCategoryName(): string
    {
        return $this->spreadsheetCategoryName;
    }
    
    /**
     * get attribute
     *
     * @return array
     */
    public function attributes(): array
    {
        return $this->attributes;
    }
    
    /**
     * set attribute
     *
     * @param  string  $value
     * @param  string  $headerName
     */
    public function setAttributes(string $value, string $headerName): void
    {
        $this->attributes[$headerName] = $value;
    }
    
    /**
     * get main key name
     *
     * @return string
     */
    public function mainKeyName(): string
    {
        return $this->mainKeyName;
    }
    
    /**
     * set main key name
     *
     * @param  string  $mainKeyName
     */
    public function setMainKeyName(string $mainKeyName): void
    {
        $this->mainKeyName = $mainKeyName;
    }
    
    /**
     * get sheet name
     *
     * @return string
     */
    public function sheetName(): string
    {
        return $this->sheetName;
    }
    
    /**
     * get sub attributes
     *
     * @return \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\SubAttribute[]
     */
    public function subAttributes(): array
    {
        return $this->subAttributes;
    }
    
    /**
     * set sub attributes
     *
     * @param  array  $subAttributes
     */
    public function setSubAttributes(array $subAttributes): void
    {
        $this->subAttributes = $subAttributes;
    }
}
