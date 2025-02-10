<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators;

use Illuminate\Console\OutputStyle;
use Illuminate\Support\Str;
use StepUpDream\DreamAbilitySupport\Console\View\Components\Task;
use StepUpDream\DreamAbilitySupport\Supports\File\FileOperation;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\interface\AreaCreatorInterface;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\interface\CreatorInterface;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\Struct\SpreadSheetConfig;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute;
use StepUpDream\SpreadSheetConverter\SpreadSheetService\Readers\SpreadSheetReader;

/**
 * Class BaseCreator.
 */
readonly class BaseCreator implements CreatorInterface
{
    /**
     * BaseCreator constructor.
     */
    public function __construct(
        private FileOperation $fileOperation,
        private SpreadSheetReader $spreadSheetReader,
        private BladeLoader $bladeLoader,
        private SpreadSheetConfig $spreadSheetConfig,
        private OutputStyle $output,
        private AreaCreatorInterface $areaCreator,
    ) {
        //
    }

    /**
     * Execution of processing.
     */
    public function run(?string $targetFileName): void
    {
        $spreadSheets = $this->spreadSheetReader()->read($this->spreadSheetConfig->sheetId());
        $spreadSheetTitle = $this->spreadSheetReader()->spreadSheetTitle($this->spreadSheetConfig->sheetId());
        $this->outputRender(sprintf('%s file load', $spreadSheetTitle));

        foreach ($spreadSheets as $sheetName => $sheet) {
            $parentAttributes = $this->convertSheetData($sheet, Str::studly($spreadSheetTitle), $sheetName);
            $this->verifySheetData($parentAttributes);
            $this->fileOperation()->createGitKeep($this->spreadSheetConfig->definitionDirectoryPath());
            foreach ($parentAttributes as $parentAttribute) {
                $outputPath = $this->areaCreator->outputPath($parentAttribute->sheetName());
                $this->createDefinitionDocument($parentAttribute, $targetFileName, $outputPath);
            }
        }

        $this->newLine();
    }

    /**
     * Verification of correct type specification.
     *
     * @param ParentAttribute[] $parentAttributes
     */
    public function verifySheetData(array $parentAttributes): void
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
     */
    public function createDefinitionDocument(
        ParentAttribute $parentAttribute,
        ?string $targetFileName,
        string $outputPath,
    ): void {
        $this->outputRender($outputPath, $this->task($parentAttribute, $targetFileName, $outputPath));
    }

    /**
     * only render the task
     */
    public function outputRender(string $text, ?string $task = null): void
    {
        (new Task($this->output))->render($text, $task);
    }

    /**
     * Executes a task based on the parent attribute and target file name, performing file operations as necessary.
     *
     * @param ParentAttribute $parentAttribute The parent attribute containing details needed for the task.
     * @param string|null $targetFileName The target file name to compare or process, if specified.
     * @return string Returns 'DONE' if the operation completes successfully, otherwise 'SKIP'.
     */
    private function task(ParentAttribute $parentAttribute, ?string $targetFileName, string $outputPath): string
    {
        $mainKeyName = collect($parentAttribute->parentAttributeDetails())->first();
        $fileName = basename($outputPath);

        // Determine initial status based on the read-skip condition.
        $resultStatus = $this->isReadSkip($mainKeyName, $targetFileName) ? 'SKIP' : 'DONE';

        if ($resultStatus === 'DONE') {
            $loadBladeFile = $this->bladeLoader->loadBladeFile(
                $this->spreadSheetConfig->useBladeFileName(),
                $parentAttribute,
            );
            if ($this->fileOperation->isContentDifferent(
                $loadBladeFile,
                $this->spreadSheetConfig->definitionDirectoryPath(),
                $fileName,
            )) {
                $this->fileOperation->createFile($loadBladeFile, $outputPath, true);
            } else {
                $resultStatus = 'SKIP';
            }
        }

        return $resultStatus;
    }

    /**
     * Whether to skip reading.
     */
    private function isReadSkip(?string $mainKeyName, ?string $targetFileName): bool
    {
        if ($targetFileName === null || $mainKeyName === null) {
            return false;
        }

        return Str::snake(pathinfo($targetFileName, PATHINFO_FILENAME)) !== Str::snake($mainKeyName);
    }

    /**
     * Add newline.
     *
     * @return $this
     */
    public function newLine(): static
    {
        $this->output->newLine();

        return $this;
    }

    /**
     * Get fileOperation.
     */
    public function fileOperation(): FileOperation
    {
        return $this->fileOperation;
    }

    /**
     * Get output.
     */
    public function output(): OutputStyle
    {
        return $this->output;
    }

    /**
     * Convert spreadsheet data.
     *
     * @param string[][] $sheet
     * @return ParentAttribute[]
     */
    protected function convertSheetData(array $sheet, string $spreadSheetTitle, string $sheetName): array
    {
        $rowNumber = 0;
        $convertedSheetData = [];
        while (!empty($sheet[$rowNumber])) {
            if ($this->spreadSheetReader()->isRowEmpty($sheet[$rowNumber])) {
                $rowNumber++;

                continue;
            }

            $convertedSheetData[] = $this->areaCreator->createParentAttribute(
                $sheet,
                $spreadSheetTitle,
                $rowNumber,
                $sheetName,
            );
        }

        return $convertedSheetData;
    }

    /**
     * Get spreadSheetReader.
     */
    public function spreadSheetReader(): SpreadSheetReader
    {
        return $this->spreadSheetReader;
    }
}
