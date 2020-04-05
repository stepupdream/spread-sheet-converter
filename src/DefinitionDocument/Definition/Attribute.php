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
    public function spreadsheetCategoryName()
    {
        return $this->spreadsheet_category_name;
    }
    
    /**
     * get attribute
     *
     * @return array
     */
    public function attributes()
    {
        return $this->attributes;
    }
    
    /**
     * set attribute
     *
     * @param string $value
     * @param string $header_name
     */
    public function setAttributes(string $value, string $header_name)
    {
        $this->attributes[$header_name] = $value;
    }
    
    /**
     * get main_key_name
     *
     * @return string
     */
    public function mainKeyName()
    {
        return $this->main_key_name;
    }
    
    /**
     * set main_key_name
     *
     * @param string $main_key_name
     */
    public function setMainKeyName(string $main_key_name)
    {
        $this->main_key_name = $main_key_name;
    }
    
    /**
     * get sub_attributes
     *
     * @return \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definition\SubAttribute[]
     */
    public function subAttributes()
    {
        return $this->sub_attributes;
    }
    
    /**
     * set sub_attributes
     *
     * @param array $sub_attributes
     */
    public function setSubAttributes(array $sub_attributes)
    {
        $this->sub_attributes = $sub_attributes;
    }
}
