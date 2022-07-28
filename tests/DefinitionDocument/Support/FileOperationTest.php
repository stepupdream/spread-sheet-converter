<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\Test\DefinitionDocument\Support;

use StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation;
use StepUpDream\SpreadSheetConverter\Test\TestCase;

class FileOperationTest extends TestCase
{
    /**
     * @test
     */
    public function shouldCreate(): void
    {
        $fileOperation = new FileOperation();
        $result = $fileOperation->shouldCreate("abcdef\n", __DIR__.'/Sample', 'sample.txt');
        self::assertEquals(false, $result);
    }
}
