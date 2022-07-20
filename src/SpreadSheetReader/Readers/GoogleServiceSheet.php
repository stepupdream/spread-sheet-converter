<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers;

class GoogleServiceSheet
{
    /**
     * @param  string  $spreadSheetTitle
     * @param  string[][][]  $spreadSheets
     */
    public function __construct(
        protected string $spreadSheetTitle,
        protected array $spreadSheets
    ) {
    }

    /**
     * Get spreadSheetTitle.
     *
     * @return string
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
