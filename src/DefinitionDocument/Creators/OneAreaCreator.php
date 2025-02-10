<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators;

use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\interface\AreaCreatorInterface;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\Struct\SpreadSheetConfig;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute;
use StepUpDream\SpreadSheetConverter\SpreadSheetService\Readers\SpreadSheetReader;

/**
 * Class OneAreaCreator.
 */
readonly class OneAreaCreator implements AreaCreatorInterface
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
        $headerNamesParent = $this->spreadSheetReader->getParentAttributeKeyName($sheet, '');

        // In the One Area setting, it is read data that has no parent-child relationship.
        // However, it is assumed that there is a parent in the class for commonality with others.
        $parentAttribute = new ParentAttribute($spreadsheetTitle, $sheetName, $headerNamesParent);
        while (!empty($sheet[$rowNumber]) && !$this->spreadSheetReader->isRowEmpty($sheet[$rowNumber])) {
            $attributes = $this->attributeGroupCreator->createAttributesGroup($sheet, $rowNumber, $headerNamesParent);
            $parentAttribute->setAttributesGroup($attributes);
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
