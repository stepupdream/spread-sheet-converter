<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions;

/**
 * Class ApiAttribute
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions
 */
class ApiAttribute
{
    /**
     * @var string
     */
    protected $spreadsheetCategoryName;
    
    /**
     * @var string
     */
    protected $sheetName;
    
    /**
     * @var array
     */
    protected $attributes = [];
    
    /**
     * @var string
     */
    protected $mainKeyName = '';
    
    /**
     * @var array
     */
    protected $requestAttributes = [];
    
    /**
     * @var array
     */
    protected $responseAttributes = [];
    
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
     * get request attributes
     *
     * @return array
     */
    public function requestAttributes(): array
    {
        return $this->requestAttributes;
    }
    
    /**
     * set requestAttributes
     *
     * @param  array  $requestAttributes
     */
    public function setRequestAttributes(array $requestAttributes): void
    {
        $this->requestAttributes = $requestAttributes;
    }
    
    /**
     * get response attributes
     *
     * @return array
     */
    public function responseAttributes(): array
    {
        return $this->responseAttributes;
    }
    
    /**
     * set responseAttributes
     *
     * @param  array  $responseAttributes
     */
    public function setResponseAttributes(array $responseAttributes): void
    {
        $this->responseAttributes = $responseAttributes;
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
}
