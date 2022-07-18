<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Definition;

/**
 * Class Attribute
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Definition
 */
class Attribute
{
    /**
     * @var string
     */
    protected $spreadsheet_category_name;
    
    /**
     * @var array
     */
    protected $attributes = [];
    
    /**
     * @var string
     */
    protected $sheet_name;
    
    /**
     * @var string
     */
    protected $main_key_name = '';
    
    /**
     * @var \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definition\SubAttribute[]
     */
    protected $sub_attributes = [];
    
    /**
     * Attribute constructor.
     *
     * @param string $spreadsheet_category_name
     */
    public function __construct(
        string $spreadsheet_category_name
    ) {
        $this->spreadsheet_category_name = $spreadsheet_category_name;
    }
    
    /**
     * get spreadsheet_tile_name
     *
     * @return string
     */
    public function spreadsheetCategoryName(): string
    {
        return $this->spreadsheet_category_name;
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
     * @param string $value
     * @param string $header_name
     * @param string $sheet_name
     */
    public function setAttributes(string $value, string $header_name, string $sheet_name): void
    {
        $this->attributes[$header_name] = $value;
        $this->sheet_name = $sheet_name;
    }
    
    /**
     * get main_key_name
     *
     * @return string
     */
    public function mainKeyName(): string
    {
        return $this->main_key_name;
    }
    
    /**
     * get sheet_name
     *
     * @return string
     */
    public function sheetName(): string
    {
        return $this->sheet_name;
    }
    
    /**
     * set main_key_name
     *
     * @param string $main_key_name
     */
    public function setMainKeyName(string $main_key_name): void
    {
        $this->main_key_name = $main_key_name;
    }
    
    /**
     * get sub_attributes
     *
     * @return \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definition\SubAttribute[]
     */
    public function subAttributes(): array
    {
        return $this->sub_attributes;
    }
    
    /**
     * set sub_attributes
     *
     * @param array $sub_attributes
     */
    public function setSubAttributes(array $sub_attributes): void
    {
        $this->sub_attributes = $sub_attributes;
    }
}
