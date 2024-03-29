<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators;

use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute;

class Other extends Base
{
    /**
     * Generate Attribute class based on Sheet data.
     *
     * @param  string[][]  $sheet
     * @param  string  $spreadsheetTitle
     * @param  int  $rowNumber
     * @param  string  $sheetName
     * @return \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute
     */
    protected function createParentAttribute(
        array $sheet,
        string $spreadsheetTitle,
        int &$rowNumber,
        string $sheetName
    ): ParentAttribute {
        $headerNamesParent = $this->spreadSheetReader->getParentAttributeKeyName($sheet, '');

        // In the Other setting, it is read data that has no parent-child relationship.
        // However, it is assumed that there is a parent in the class for commonality with others.
        $parentAttribute = new ParentAttribute($spreadsheetTitle, $sheetName, $headerNamesParent);
        while (! empty($sheet[$rowNumber]) && ! $this->spreadSheetReader->isAllEmpty($sheet[$rowNumber])) {
            $attributes = $this->createAttributesGroup($sheet, $rowNumber, $headerNamesParent);
            $parentAttribute->setAttributesGroup($attributes);
        }

        return $parentAttribute;
    }

    /**
     * File output destination.
     *
     * @param  ParentAttribute  $parentAttribute
     * @return string
     */
    protected function outputPath(ParentAttribute $parentAttribute): string
    {
        $fileName = $parentAttribute->sheetName().'.yml';

        return $this->outputDirectoryPath.DIRECTORY_SEPARATOR.$fileName;
    }
}
