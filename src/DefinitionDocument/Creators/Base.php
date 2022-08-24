<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators;

use Illuminate\Console\OutputStyle;
use Illuminate\Console\View\Components\Info;
use Illuminate\Support\Str;
use LogicException;
use StepUpDream\DreamAbilitySupport\Supports\File\FileOperation;
use StepUpDream\DreamAbilitySupport\Supports\File\Task;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute;
use StepUpDream\SpreadSheetConverter\SpreadSheetService\Readers\SpreadSheetReader;

abstract class Base
{
    /**
     * The output style implementation.
     *
     * @var \Illuminate\Console\OutputStyle
     */
    protected OutputStyle $output;

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
     * Identifier to identify the loaded sheet.
     *
     * @var string
     */
    protected string $categoryTag;

    /**
     * BaseCreator constructor.
     *
     * @param  \StepUpDream\DreamAbilitySupport\Supports\File\FileOperation  $fileOperation
     * @param  \StepUpDream\SpreadSheetConverter\SpreadSheetService\Readers\SpreadSheetReader  $spreadSheetReader
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
        $this->categoryTag = $readSpreadSheet['category_tag'];
    }

    /**
     * Execution of processing.
     *
     * @param  string|null  $targetFileName
     */
    public function run(?string $targetFileName): void
    {
        $requestRuleSheetName = config('stepupdream.spread-sheet-converter.request_rule_sheet_name');
        $spreadSheets = $this->spreadSheetReader->read($this->sheetId);
        $spreadSheetTitle = $this->spreadSheetReader->spreadSheetTitle($this->sheetId);
        (new Info($this->output))->render(sprintf('%s file load', $spreadSheetTitle));

        foreach ($spreadSheets as $sheetName => $sheet) {
            if (! empty($requestRuleSheetName) && $sheetName === $requestRuleSheetName) {
                continue;
            }

            $parentAttributes = $this->convertSheetData($sheet, Str::studly($spreadSheetTitle), $sheetName);
            $this->verifySheetData($parentAttributes);
            $this->fileOperation->createGitKeep($this->definitionDirectoryPath);
            $this->createDefinitionDocument($parentAttributes, $targetFileName);
        }

        $this->output->newLine();
    }

    /**
     * Convert spreadsheet data.
     *
     * @param  string[][]  $sheet
     * @param  string  $spreadSheetTitle
     * @param  string  $sheetName
     * @return \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute[]
     */
    protected function convertSheetData(array $sheet, string $spreadSheetTitle, string $sheetName): array
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
     * @return void
     */
    protected function createDefinitionDocument(array $parentAttributes, ?string $targetFileName): void
    {
        foreach ($parentAttributes as $parentAttribute) {
            $description = $this->outputPath($parentAttribute);
            (new Task($this->output))->render($description, function () use ($parentAttribute, $targetFileName) {
                $mainKeyName = collect($parentAttribute->parentAttributeDetails())->first();
                $outputPath = $this->outputPath($parentAttribute);
                $fileName = basename($outputPath);

                // If there is a specification to get only a part, skip other data
                if ($this->isReadSkip($mainKeyName, $targetFileName)) {
                    return 'SKIP';
                }
                $loadBladeFile = $this->loadBladeFile($this->useBladeFileName, $parentAttribute);

                if ($this->fileOperation->shouldCreate($loadBladeFile, $this->definitionDirectoryPath, $fileName)) {
                    $this->fileOperation->createFile($loadBladeFile, $outputPath, true);

                    return 'DONE';
                }

                return 'SKIP';
            });
        }
    }

    /**
     * File output destination.
     *
     * @param  \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute  $parentAttribute
     * @return string
     */
    protected function outputPath(ParentAttribute $parentAttribute): string
    {
        $mainKeyName = collect($parentAttribute->parentAttributeDetails())->first();

        if ($mainKeyName === null) {
            throw new LogicException('mainKeyName was not found');
        }

        $fileName = $mainKeyName.'.yml';

        return $this->outputDirectoryPath.
            DIRECTORY_SEPARATOR.
            Str::studly($parentAttribute->sheetName()).
            DIRECTORY_SEPARATOR.$fileName;
    }

    /**
     * Whether to skip reading.
     *
     * @param  string|null  $mainKeyName
     * @param  string|null  $targetFileName
     * @return bool
     */
    protected function isReadSkip(?string $mainKeyName, ?string $targetFileName): bool
    {
        if ($targetFileName === null || $mainKeyName === null) {
            return false;
        }

        return Str::snake(pathinfo($targetFileName, PATHINFO_FILENAME)) !== Str::snake($mainKeyName);
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

    /**
     * Set the output implementation that should be used by the console.
     *
     * @param  \Illuminate\Console\OutputStyle  $output
     * @return $this
     */
    public function setOutput(OutputStyle $output): static
    {
        $this->output = $output;

        return $this;
    }
}
