<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators;

use Illuminate\Support\Str;
use LogicException;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers\SpreadSheetReader;

abstract class Base
{
    /**
     * Template blade file to use.
     *
     * @var string
     */
    protected string $useBladeFileName;

    /**
     * Google Spreadsheet sheetID.
     *
     * @var string
     */
    protected string $sheetId;

    /**
     * Output destination of yaml file.
     *
     * @var string
     */
    protected string $outputDirectoryPath;

    /**
     * Delimiter column header name.
     *
     * @var string
     */
    protected string $separationKey;

    /**
     * Key name of the group.
     *
     * @var string
     */
    protected string $attributeGroupColumnName;

    /**
     * @var string
     */
    protected string $definitionDirectoryPath;

    /**
     * BaseCreator constructor.
     *
     * @param  \StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation  $fileOperation
     * @param  \StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers\SpreadSheetReader  $spreadSheetReader
     * @param  string[]  $readSpreadSheet
     */
    public function __construct(
        protected FileOperation $fileOperation,
        protected SpreadSheetReader $spreadSheetReader,
        array $readSpreadSheet
    ) {
        $this->useBladeFileName = $readSpreadSheet['use_blade'];
        $this->sheetId = $readSpreadSheet['sheet_id'];
        $this->outputDirectoryPath = $readSpreadSheet['output_directory_path'];
        $this->definitionDirectoryPath = $readSpreadSheet['definition_directory_path'];
        $this->separationKey = $readSpreadSheet['separation_key'];
        $this->attributeGroupColumnName = $readSpreadSheet['attribute_group_column_name'] ?? '';
    }

    /**
     * Execution of processing.
     *
     * @param  string|null  $targetFileName
     */
    public function run(?string $targetFileName): void
    {
        $spreadSheets = $this->spreadSheetReader->read($this->sheetId);
        $spreadSheetTitle = $this->spreadSheetReader->spreadSheetTitle($this->sheetId);
        foreach ($spreadSheets as $sheetName => $sheet) {
            $parentAttributes = $this->convertSheetData($sheet, Str::studly($spreadSheetTitle), $sheetName);
            $this->verifySheetData($parentAttributes);
            $this->createDefinitionDocument($parentAttributes, $targetFileName);
        }
    }

    /**
     * Convert spreadsheet data.
     *
     * @param  string[][]  $sheet
     * @param  string  $spreadSheetTitle
     * @param  string  $sheetName
     * @return \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute[]
     */
    public function convertSheetData(array $sheet, string $spreadSheetTitle, string $sheetName): array
    {
        $rowNumber = 0;
        $convertedSheetData = [];

        while (! empty($sheet[$rowNumber])) {
            if ($this->spreadSheetReader->isAllEmpty($sheet[$rowNumber])) {
                $rowNumber++;
                continue;
            }

            $convertedSheetData[] = $this->createParentAttribute($sheet, $spreadSheetTitle, $rowNumber, $sheetName);
        }

        return $convertedSheetData;
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
        $headerNamesParent = $this->spreadSheetReader->getParentAttributeKeyName($sheet, $this->separationKey);
        $headerNamesChild = $this->spreadSheetReader->getAttributeKeyName($sheet, $this->separationKey);
        $mainKeyNameChild = collect($headerNamesChild)->first();

        $parentAttribute = new ParentAttribute($spreadsheetTitle, $sheetName, $headerNamesChild);
        foreach ($headerNamesParent as $headerNameParent) {
            $parentAttribute->setParentAttributeDetails($sheet[$rowNumber][$headerNameParent], $headerNameParent);
        }

        while (! empty($sheet[$rowNumber]) && ! $this->spreadSheetReader->isAllEmpty($sheet[$rowNumber])) {
            $groupKeyName = $sheet[$rowNumber][$mainKeyNameChild];
            $attributes = $this->createAttributesGroup($sheet, $rowNumber, $headerNamesChild);

            if (empty($this->attributeGroupColumnName)) {
                $parentAttribute->setAttributesGroup($attributes);
            } else {
                $parentAttribute->setAttributesGroup($attributes, $groupKeyName);
            }
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
        $mainKeyName = (string) collect($headerNames)->first();
        $beforeMainKeyData = $sheet[$rowNumber][$headerNames[$mainKeyName]];

        while (true) {
            $attribute = new Attribute();
            foreach ($headerNames as $headerName) {
                $attribute->setAttributeDetails($sheet[$rowNumber][$headerName], $headerName);
            }

            if ($this instanceof MultiGroup) {
                $attribute->unsetAttributeDetail($this->attributeGroupColumnName);
                $message = $this->createRuleMessage($sheet, $rowNumber);
                $attribute->setRuleMessage($message);
            }

            if (! $this->spreadSheetReader->isAllEmpty($attribute->attributeDetails())) {
                $attributes[] = $attribute;
            }
            $rowNumber++;

            if (empty($sheet[$rowNumber]) || $this->spreadSheetReader->isAllEmpty($sheet[$rowNumber])) {
                break;
            }

            // If the key of the group is switched, it is judged that the group is finished
            if (! empty($this->attributeGroupColumnName) &&
                $sheet[$rowNumber][$headerNames[$mainKeyName]] !== '' &&
                $sheet[$rowNumber][$headerNames[$mainKeyName]] !== $beforeMainKeyData) {
                break;
            }
        }

        return $attributes;
    }

    /**
     * Verification of correct type specification.
     *
     * @param  \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute[]  $parentAttributes
     */
    protected function verifySheetData(array $parentAttributes): void
    {
        foreach ($parentAttributes as $parentAttribute) {
            foreach ($parentAttribute->attributesGroup() as $attributes) {
                foreach ($attributes as $attribute) {
                    $this->spreadSheetReader->verifySheetDataDetail($attribute->attributeDetails());
                }
            }
        }
    }


    /**
     * Generate a definition document.
     *
     * @param  \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute[]  $parentAttributes
     * @param  string|null  $targetFileName
     */
    public function createDefinitionDocument(array $parentAttributes, ?string $targetFileName): void
    {
        $this->fileOperation->createGitKeep($this->definitionDirectoryPath);
        foreach ($parentAttributes as $parentAttribute) {
            $mainKeyName = collect($parentAttribute->parentAttributeDetails())->first();

            if ($mainKeyName === null) {
                throw new LogicException('mainKeyName was not found');
            }

            // If there is a specification to get only a part, skip other data
            if ($this->isReadSkip($mainKeyName, $targetFileName)) {
                continue;
            }
            $fileName = $mainKeyName.'.yaml';
            $targetPath = $this->outputDirectoryPath.
                DIRECTORY_SEPARATOR.
                Str::studly($parentAttribute->sheetName()).
                DIRECTORY_SEPARATOR.$fileName;
            $loadBladeFile = $this->loadBladeFile($this->useBladeFileName, $parentAttribute);
            if (! $this->fileOperation->shouldCreate($loadBladeFile, $this->definitionDirectoryPath, $fileName)) {
                continue;
            }

            $this->fileOperation->createFile($loadBladeFile, $targetPath, true);
        }
    }

    /**
     * Whether to skip reading.
     *
     * @param  string  $mainKeyName
     * @param  string|null  $targetFileName
     * @return bool
     */
    protected function isReadSkip(string $mainKeyName, ?string $targetFileName): bool
    {
        if ($targetFileName === null) {
            return false;
        }

        return Str::snake($targetFileName) !== Str::snake($mainKeyName);
    }

    /**
     * Read the blade file.
     *
     * @param  string  $useBladeFileName
     * @param  \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute  $parentAttribute
     * @return string
     */
    protected function loadBladeFile(string $useBladeFileName, ParentAttribute $parentAttribute): string
    {
        return view('spread-sheet-converter::'.Str::snake($useBladeFileName), [
            'parentAttribute' => $parentAttribute,
        ])->render();
    }
}
