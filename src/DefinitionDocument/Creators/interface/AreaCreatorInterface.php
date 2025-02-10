<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\interface;

use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute;

/**
 * Interface AreaCreatorInterface
 */
interface AreaCreatorInterface
{
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
    ): ParentAttribute;

    /**
     * File output destination.
     */
    public function outputPath(string $sheetName): string;
}
