<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers;

use Illuminate\Console\OutputStyle;
use StepUpDream\DreamAbilitySupport\Supports\File\Task;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class SpreadSheetWriter
{
    /**
     * @param  \StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers\GoogleService  $googleService
     */
    public function __construct(
        protected GoogleService $googleService,
    ) {
    }

    /**
     * Read spreadsheet data.
     *
     * @param  string  $sheetId
     * @param  array  $values
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
        $bufferedOutput = new BufferedOutput();
        $style = new OutputStyle(new ArrayInput([]), $bufferedOutput);

        (new Task($style))->render(
            'write: '.$sheetName,
            fn () => $this->googleService->appendGoogleServiceSheet($sheetId, $values, $sheetName, $range, $option)
        );
    }

    /**
     * Update spreadsheet data.
     *
     * @param  string  $sheetId
     * @param  array  $values
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
        $bufferedOutput = new BufferedOutput();
        $style = new OutputStyle(new ArrayInput([]), $bufferedOutput);

        (new Task($style))->render(
            'write: '.$sheetName,
            fn () => $this->googleService->updateGoogleServiceSheet($sheetId, $values, $sheetName, $range, $option)
        );
    }
}
