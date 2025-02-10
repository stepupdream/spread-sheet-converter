<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators;

use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\interface\AreaCreatorInterface;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\Struct\SpreadSheetConfig;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute;
use StepUpDream\SpreadSheetConverter\SpreadSheetService\Readers\SpreadSheetReader;

/**
 * Class TwoAreaCreator.
 */
readonly class TwoAreaCreator implements AreaCreatorInterface
{
    /**
     * OneAreaCreator constructor.
     */
    public function __construct(
        private SpreadSheetReader $spreadSheetReader,
        private AttributeGroupCreator $attributeGroupCreator,
        private SpreadSheetConfig $spreadSheetConfig,
    ) {
    }

    /**
     * Generate Attribute class based on Sheet data.
     *
     * @param string[][] $sheet
     */
    public function createParentAttribute(
        array $sheet,
        string $spreadsheetTitle,
        int &$rowNumber,
        string $sheetName,
    ): ParentAttribute {
        $headerNamesParent = $this->spreadSheetReader->getParentAttributeKeyName($sheet, $this->spreadSheetConfig->separationKey());
        $headerNamesChild = $this->spreadSheetReader->getAttributeKeyName($sheet, $this->spreadSheetConfig->separationKey());
        $mainKeyNameChild = collect($headerNamesChild)->first();
        $parentAttribute = new ParentAttribute($spreadsheetTitle, $sheetName, $headerNamesChild);
        foreach ($headerNamesParent as $headerNameParent) {
            $parentAttribute->setParentAttributeDetails($sheet[$rowNumber][$headerNameParent], $headerNameParent);
        }

        while (!empty($sheet[$rowNumber]) && !$this->spreadSheetReader->isRowEmpty($sheet[$rowNumber])) {
            $groupKeyName = $sheet[$rowNumber][$mainKeyNameChild];
            $attributes = $this->attributeGroupCreator->createAttributesGroup($sheet, $rowNumber, $headerNamesChild);
            if (empty($this->attributeGroupColumnName)) {
                $parentAttribute->setAttributesGroup($attributes);
            } else {
                $parentAttribute->setAttributesGroup($attributes, $groupKeyName);
            }
        }

        return $parentAttribute;
    }

    /**
     * File output destination.
     */
    public function outputPath(string $sheetName): string
    {
        $fileName = sprintf('%s.%s', $sheetName, $this->spreadSheetConfig->fileExtension());

        return $this->spreadSheetConfig->outputDirectoryPath() . DIRECTORY_SEPARATOR . $fileName;
    }
}
