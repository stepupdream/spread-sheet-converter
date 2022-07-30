<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators;

use LogicException;
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
     * Generate a definition document.
     *
     * @param  \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute[]  $parentAttributes
     * @param  string|null  $targetFileName
     */
    public function createDefinitionDocument(array $parentAttributes, ?string $targetFileName): void
    {
        if (count($parentAttributes) > 1) {
            throw new LogicException('Other does not support multiple parent settings. : '.$this->categoryTag);
        }

        $this->fileOperation->createGitKeep($this->definitionDirectoryPath);
        foreach ($parentAttributes as $parentAttribute) {
            $fileName = $parentAttribute->sheetName().'.yml';
            $targetPath = $this->outputDirectoryPath.
                DIRECTORY_SEPARATOR.
                $fileName;
            $loadBladeFile = $this->loadBladeFile($this->useBladeFileName, $parentAttribute);
            if (! $this->fileOperation->shouldCreate($loadBladeFile, $this->definitionDirectoryPath, $fileName)) {
                continue;
            }
            $this->fileOperation->createFile($loadBladeFile, $targetPath, true);
        }
    }
}
