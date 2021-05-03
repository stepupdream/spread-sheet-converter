<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions;

/**
 * Class ParentAttribute
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions
 */
class ParentAttribute
{
    /**
     * Category name for classification.
     *
     * @var string
     */
    protected $spreadsheetCategoryName;
    
    /**
     * Array of contents by column.
     *
     * @var array
     */
    protected $parentAttributeDetails = [];
    
    /**
     * GoogleSpreadSheet sheet name
     *
     * @var string
     */
    protected $sheetName;
    
    /**
     * The Attribute instance array.
     *
     * @var \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute[][]
     */
    protected $attributesGroup = [];
    
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
     * Get spreadsheet category name. (Specified in the config file)
     *
     * @return string
     */
    public function spreadsheetCategoryName(): string
    {
        return $this->spreadsheetCategoryName;
    }
    
    /**
     * Get parent attribute detail.
     *
     * @return array
     */
    public function parentAttributeDetails(): array
    {
        return $this->parentAttributeDetails;
    }
    
    /**
     * Set parent attribute detail.
     *
     * @param  string  $value
     * @param  string  $headerName
     */
    public function setParentAttributeDetails(string $value, string $headerName): void
    {
        $this->parentAttributeDetails[$headerName] = $value;
    }
    
    /**
     * Get sheet name.
     *
     * @return string
     */
    public function sheetName(): string
    {
        return $this->sheetName;
    }
    
    /**
     * Get attributes group.
     *
     * @return \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute[][]
     */
    public function attributesGroup(): array
    {
        return $this->attributesGroup;
    }
    
    /**
     * Set attributes group.
     *
     * @param  array  $attributesGroup
     * @param  string  $groupKey
     */
    public function setAttributesGroup(array $attributesGroup, string $groupKey = '*'): void
    {
        $this->attributesGroup[$groupKey] = $attributesGroup;
    }
    
    /**
     * Get attributes group.
     *
     * @return \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute[]
     */
    public function getAttributesGroupByKeyName(string $groupKey): array
    {
        return $this->attributesGroup[$groupKey];
    }
}
