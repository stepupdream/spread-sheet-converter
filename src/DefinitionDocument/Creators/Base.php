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
     */
    protected OutputStyle $output;

    /**
     * Template blade file to use.
     */
    protected string $useBladeFileName;

    /**
     * Google Spreadsheet sheetID.
     */
    protected string $sheetId;

    /**
     * Output destination of yaml file.
     */
    protected string $outputDirectoryPath;

    /**
     * Path of the directory where the definition is stored.
     */
    protected string $definitionDirectoryPath;

    /**
     * Identifier to identify the loaded sheet.
     */
    protected string $categoryTag;

    /**
     * File_extension.
     */
    protected string $fileExtension;

    /**
     * BaseCreator constructor.
     *
     * @param  string[]  $readSpreadSheet
     */
    public function __construct(
        protected FileOperation $fileOperation,
        protected SpreadSheetReader $spreadSheetReader,
        protected BladeLoader $bladeLoader,
        array $readSpreadSheet,
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
     */
    protected function createDefinitionDocument(array $parentAttributes, ?string $targetFileName): void
    {
        foreach ($parentAttributes as $parentAttribute) {
            $description = $this->outputPath($parentAttribute);

            (new Task($this->output))->render($description, $this->task($parentAttribute, $targetFileName));
        }
    }

    /**
     * Executes a task based on the parent attribute and target file name, performing file operations as necessary.
     *
     * @param  ParentAttribute  $parentAttribute  The parent attribute containing details needed for the task.
     * @param  string|null  $targetFileName  The target file name to compare or process, if specified.
     * @return string Returns 'DONE' if the operation completes successfully, otherwise 'SKIP'.
     */
    private function task(ParentAttribute $parentAttribute, ?string $targetFileName): string
    {
        $mainKeyName = collect($parentAttribute->parentAttributeDetails())->first();
        $outputPath = $this->outputPath($parentAttribute);
        $fileName = basename($outputPath);

        // If there is a specification to get only a part, skip other data
        if ($this->isReadSkip($mainKeyName, $targetFileName)) {
            return 'SKIP';
        }
        $loadBladeFile = $this->bladeLoader->loadBladeFile($this->useBladeFileName, $parentAttribute);

        if ($this->fileOperation->isContentDifferent($loadBladeFile, $this->definitionDirectoryPath, $fileName)) {
            $this->fileOperation->createFile($loadBladeFile, $outputPath, true);

            return 'DONE';
        }

        return 'SKIP';
    }

    /**
     * Whether to skip reading.
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
     * @return $this
     */
    public function setOutput(OutputStyle $output): static
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Creates and returns the parent attribute for the specified spreadsheet.
     *
     * @param  string[][]  $sheet  The data of the current sheet.
     * @param  string  $spreadsheetTitle  The title of the spreadsheet.
     * @param  int  &$rowNumber  The current row number, passed by reference.
     * @param  string  $sheetName  The name of the sheet.
     * @return ParentAttribute The created parent attribute object.
     */
    abstract protected function createParentAttribute(
        array $sheet,
        string $spreadsheetTitle,
        int &$rowNumber,
        string $sheetName,
    ): ParentAttribute;

    /**
     * File output destination.
     */
    abstract protected function outputPath(ParentAttribute $parentAttribute): string;
}
