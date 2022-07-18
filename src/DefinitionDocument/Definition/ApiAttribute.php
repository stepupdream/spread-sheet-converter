<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Definition;

/**
 * Class ApiAttribute
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Definition
 */
class ApiAttribute
{
    /**
     * @var string
     */
    protected $spreadsheet_category_name;
    
    /**
     * @var string
     */
    protected $sheet_name;
    
    /**
     * @var array
     */
    protected $attributes = [];
    
    /**
     * @var string
     */
    protected $main_key_name = '';
    
    /**
     * @var array
     */
    protected $request_attributes = [];
    
    /**
     * @var array
     */
    protected $response_attributes = [];
    
    /**
     * Attribute constructor.
     *
     * @param string $spreadsheet_category_name
     * @param string $sheet_name
     */
    public function __construct(
        string $spreadsheet_category_name,
        string $sheet_name
    ) {
        $this->spreadsheet_category_name = $spreadsheet_category_name;
        $this->sheet_name = $sheet_name;
    }
    
    /**
     * get spreadsheet_tile_name
     *
     * @return string
     */
    public function spreadsheetCategoryName() : string
    {
        return $this->spreadsheet_category_name;
    }
    
    /**
     * get attribute
     *
     * @return array
     */
    public function attributes() : array
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
    public function mainKeyName() : string
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
     * get request_attributes
     *
     * @return array
     */
    public function requestAttributes() : array
    {
        return $this->request_attributes;
    }
    
    /**
     * set request_attributes
     *
     * @param array $request_attributes
     */
    public function setRequestAttributes(array $request_attributes)
    {
        $this->request_attributes = $request_attributes;
    }
    
    /**
     * get response_attributes
     *
     * @return array
     */
    public function responseAttributes() : array
    {
        return $this->response_attributes;
    }
    
    /**
     * set response_attributes
     *
     * @param array $response_attributes
     */
    public function setResponseAttributes(array $response_attributes)
    {
        $this->response_attributes = $response_attributes;
    }
    
    /**
     * get sheet_name
     *
     * @return string
     */
    public function sheetName() : string
    {
        return $this->sheet_name;
    }
}
