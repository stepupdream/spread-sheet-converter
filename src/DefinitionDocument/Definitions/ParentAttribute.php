<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions;

class ParentAttribute extends BaseAttribute
{
    /**
     * Array of contents by column.
     *
     * @var string[]
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
     * @param  string[]  $headerNamesChild
     */
    public function __construct(
        protected string $spreadsheetTitle,
        protected string $sheetName,
        protected array $headerNamesChild,
    ) {
        //
    }

    /**
     * Get spreadsheet title name. (Specified in the config file).
     *
     * @noinspection PhpUnused
     */
    public function spreadsheetTitle(): string
    {
        return $this->spreadsheetTitle;
    }

    /**
     * Get spreadsheet header names.
     *
     * @return string[]
     * @noinspection PhpUnused
     */
    public function headerNamesChild(): array
    {
        return $this->headerNamesChild;
    }

    /**
     * Get parent attribute detail.
     *
     * @return string[]
     */
    public function parentAttributeDetails(): array
    {
        return $this->parentAttributeDetails;
    }

    /**
     * Set parent attribute detail.
     */
    public function setParentAttributeDetails(string $value, string $headerName): void
    {
        $this->parentAttributeDetails[$headerName] = $value;
    }

    /**
     * Get parent attribute detail by header key.
     *
     * @noinspection PhpUnused
     */
    public function getParentAttributeDetailByKey(string $headerKey): string
    {
        return $this->attributeByKey($this->parentAttributeDetails, $headerKey);
    }

    /**
     * Get parent attribute detail by header key.
     *
     * @return mixed[]
     * @noinspection PhpUnused
     */
    public function getParentAttributeDetailJsonByKey(string $headerKey): array
    {
        return $this->attributeJsonByKey($this->parentAttributeDetails, $headerKey);
    }

    /**
     * Get sheet name.
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
     * @param  \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute[]  $attributesGroup
     */
    public function setAttributesGroup(array $attributesGroup, string $groupKey = '*'): void
    {
        $this->attributesGroup[$groupKey] = $attributesGroup;
    }

    /**
     * Get attributes group.
     *
     * @return \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute[]
     * @noinspection PhpUnused
     */
    public function getAttributesGroupByKeyName(string $groupKey): array
    {
        return $this->attributesGroup[$groupKey];
    }
}
