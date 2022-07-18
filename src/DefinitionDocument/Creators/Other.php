<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators;

use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute;

class Other extends Base
{
    /**
     * Generate Attribute class based on Sheet data.
     *
     * @param  array  $sheet
     * @param  string  $spreadsheetCategoryName
     * @param  int  $rowNumber
     * @param  string  $sheetName
     * @return \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute
     */
    protected function createParentAttribute(
        array $sheet,
        string $spreadsheetCategoryName,
        int &$rowNumber,
        string $sheetName
    ): ParentAttribute {
        $headerNamesParent = $this->spreadSheetReader->getParentAttributeKeyName($sheet, '');

        $parentAttribute = new ParentAttribute($spreadsheetCategoryName, $sheetName);
        foreach ($headerNamesParent as $headerNameParent) {
            $parentAttribute->setParentAttributeDetails($sheet[$rowNumber][$headerNameParent], $headerNameParent);
        }

        while (! empty($sheet[$rowNumber]) && ! $this->spreadSheetReader->isAllEmpty($sheet[$rowNumber])) {
            $attributes = $this->createAttributesGroup($sheet, $rowNumber, $headerNamesParent);
            $parentAttribute->setAttributesGroup($attributes);
        }

        return $parentAttribute;
    }
}
