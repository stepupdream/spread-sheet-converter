<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\SpreadSheetService;

class GoogleServiceSheet
{
    /**
     * Constructor method for initializing the spreadsheet title and sheets.
     *
     * @param string[][][] $spreadSheets
     */
    public function __construct(
        protected string $spreadSheetTitle,
        protected array $spreadSheets,
    ) {
        //
    }

    /**
     * Get spreadSheetTitle.
     */
    public function spreadSheetTitle(): string
    {
        return $this->spreadSheetTitle;
    }

    /**
     * Get spreadSheets.
     *
     * key : Sheet name.
     *
     * @return string[][][]
     */
    public function spreadSheets(): array
    {
        return $this->spreadSheets;
    }
}
