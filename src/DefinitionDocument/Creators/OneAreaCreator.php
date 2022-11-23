<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators;

use Illuminate\Console\View\Components\Info;
use Illuminate\Support\Str;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute;

class OneAreaCreator extends Base
{
    /**
     * Execution of processing.
     *
     * @param  string|null  $targetFileName
     */
    public function run(?string $targetFileName): void
    {
        $spreadSheets = $this->spreadSheetReader->read($this->sheetId);
        $spreadSheetTitle = $this->spreadSheetReader->spreadSheetTitle($this->sheetId);
        (new Info($this->output))->render(sprintf('%s file load', $spreadSheetTitle));

        foreach ($spreadSheets as $sheetName => $sheet) {
            $parentAttributes = $this->convertSheetData($sheet, Str::studly($spreadSheetTitle), $sheetName);
            $this->verifySheetData($parentAttributes);
            $this->fileOperation->createGitKeep($this->definitionDirectoryPath);
            $this->createDefinitionDocument($parentAttributes, $targetFileName);
        }

        $this->output->newLine();
    }

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

        // In the One Area setting, it is read data that has no parent-child relationship.
        // However, it is assumed that there is a parent in the class for commonality with others.
        $parentAttribute = new ParentAttribute($spreadsheetTitle, $sheetName, $headerNamesParent);
        while (! empty($sheet[$rowNumber]) && ! $this->spreadSheetReader->isAllEmpty($sheet[$rowNumber])) {
            $attributes = $this->createAttributesGroup($sheet, $rowNumber, $headerNamesParent);
            $parentAttribute->setAttributesGroup($attributes);
        }

        return $parentAttribute;
    }

    /**
     * Create only one attributes group.
     *
     * @param  string[][]  $sheet
     * @param  int  $rowNumber
     * @param  string[]  $headerNames
     * @return \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute[]
     */
    protected function createAttributesGroup(array $sheet, int &$rowNumber, array $headerNames): array
    {
        $attributes = [];

        while (! empty($sheet[$rowNumber]) && ! $this->spreadSheetReader->isAllEmpty($sheet[$rowNumber])) {
            $attribute = new Attribute();
            foreach ($headerNames as $headerName) {
                $attribute->setAttributeDetails($sheet[$rowNumber][$headerName], $headerName);
            }

            if (! $this->spreadSheetReader->isAllEmpty($attribute->attributeDetails())) {
                $attributes[] = $attribute;
            }
            $rowNumber++;
        }

        return $attributes;
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
