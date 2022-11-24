<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators;

use Illuminate\Console\OutputStyle;
use Illuminate\Support\Str;
use StepUpDream\DreamAbilitySupport\Console\View\Components\Task;
use StepUpDream\DreamAbilitySupport\Supports\File\FileOperation;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute;
use StepUpDream\SpreadSheetConverter\SpreadSheetService\Readers\SpreadSheetReader;

abstract class Base implements CreatorInterface
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
     * Path of the directory where the definition is stored.
     *
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
     * File_extension.
     *
     * @var string
     */
    protected string $fileExtension;

    /**
     * BaseCreator constructor.
     *
     * @param  \StepUpDream\DreamAbilitySupport\Supports\File\FileOperation  $fileOperation
     * @param  \StepUpDream\SpreadSheetConverter\SpreadSheetService\Readers\SpreadSheetReader  $spreadSheetReader
     * @param  \StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\BladeLoader  $bladeLoader
     * @param  string[]  $readSpreadSheet
     */
    public function __construct(
        protected FileOperation $fileOperation,
        protected SpreadSheetReader $spreadSheetReader,
        protected BladeLoader $bladeLoader,
        array $readSpreadSheet
    ) {
        $this->useBladeFileName = $readSpreadSheet['use_blade'];
        $this->sheetId = $readSpreadSheet['sheet_id'];
        $this->outputDirectoryPath = $readSpreadSheet['output_directory_path'];
        $this->definitionDirectoryPath = $readSpreadSheet['definition_directory_path'];
        $this->categoryTag = $readSpreadSheet['category_tag'];
        $this->fileExtension = $readSpreadSheet['file_extension'];
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
                $loadBladeFile = $this->bladeLoader->loadBladeFile($this->useBladeFileName, $parentAttribute);

                if ($this->fileOperation->shouldCreate($loadBladeFile, $this->definitionDirectoryPath, $fileName)) {
                    $this->fileOperation->createFile($loadBladeFile, $outputPath, true);

                    return 'DONE';
                }

                return 'SKIP';
            });
        }
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

    /**
     * Generate Attribute class based on Sheet data.
     *
     * @param  string[][]  $sheet
     * @param  string  $spreadsheetTitle
     * @param  int  $rowNumber
     * @param  string  $sheetName
     * @return \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute
     */
    abstract protected function createParentAttribute(
        array $sheet,
        string $spreadsheetTitle,
        int &$rowNumber,
        string $sheetName
    ): ParentAttribute;

    /**
     * File output destination.
     *
     * @param  ParentAttribute  $parentAttribute
     * @return string
     */
    abstract protected function outputPath(ParentAttribute $parentAttribute): string;
}
