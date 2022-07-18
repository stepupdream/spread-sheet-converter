<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions;

class ParentAttribute extends BaseAttribute
{
    /**
     * Array of contents by column.
     *
     * @var array
     */
    protected array $parentAttributeDetails = [];

    /**
     * The Attribute instance array.
     *
     * @var \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute[][]
     */
    protected array $attributesGroup = [];

    /**
     * Attribute constructor.
     *
     * @param  string  $spreadsheetCategoryName
     * @param  string  $sheetName
     */
    public function __construct(
        protected string $spreadsheetCategoryName,
        protected string $sheetName
    ) {
    }

    /**
     * Get spreadsheet category name. (Specified in the config file).
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
     * Get parent attribute details by header key.
     *
     * @param  string  $headerKey
     * @return string|array
     */
    public function getParentAttributeDetailsByKey(string $headerKey): string|array
    {
        return $this->attributeByKey($this->parentAttributeDetails, $headerKey);
    }

    /**
     * Get parent attribute details by header key.
     *
     * @param  string  $headerKey
     * @return array
     */
    public function getParentAttributeDetailsArrayByKey(string $headerKey): array
    {
        return $this->attributesByKey($this->parentAttributeDetails, $headerKey);
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
