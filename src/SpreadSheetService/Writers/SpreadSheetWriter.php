<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\SpreadSheetService\Writers;

use Illuminate\Console\OutputStyle;
use StepUpDream\DreamAbilitySupport\Console\View\Components\Task;
use StepUpDream\SpreadSheetConverter\SpreadSheetService\GoogleService;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class SpreadSheetWriter
{
    /**
     * @param  \StepUpDream\SpreadSheetConverter\SpreadSheetService\GoogleService  $googleService
     */
    public function __construct(
        protected GoogleService $googleService,
    ) {
    }

    /**
     * Read spreadsheet data.
     *
     * @param  string  $sheetId
     * @param  mixed[][]  $values
     * @param  string  $sheetName
     * @param  string  $range
     * @param  string  $option
     */
    public function write(
        string $sheetId,
        array $values,
        string $sheetName,
        string $range = 'A1',
        string $option = 'USER_ENTERED'
    ): void {
        $style = new OutputStyle(new ArgvInput(), new ConsoleOutput());

        (new Task($style))->render(
            'write: '.$sheetName,
            fn () => $this->googleService->appendGoogleServiceSheet($sheetId, $values, $sheetName, $range, $option)
        );
    }

    /**
     * Update spreadsheet data.
     *
     * @param  string  $sheetId
     * @param  mixed[]  $values
     * @param  string  $sheetName
     * @param  string  $range
     * @param  string  $option
     */
    public function update(
        string $sheetId,
        array $values,
        string $sheetName,
        string $range = 'A1',
        string $option = 'USER_ENTERED'
    ): void {
        $style = new OutputStyle(new ArgvInput(), new ConsoleOutput());

        (new Task($style))->render(
            'write: '.$sheetName,
            fn () => $this->googleService->updateGoogleServiceSheet($sheetId, $values, $sheetName, $range, $option)
        );
    }
}
