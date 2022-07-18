<?php

namespace StepUpDream\SpreadSheetConverter\Test\DefinitionDocument\Supports;

use StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation;
use StepUpDream\SpreadSheetConverter\Test\TestCase;

/**
 * Class FileOperationTest
 *
 * @package StepUpDream\SpreadSheetConverter\Test\DefinitionDocument\Supports
 */
class FileOperationTest extends TestCase
{
    public function testGetAllFilePath()
    {
        $fileOperation = new FileOperation();
        $paths = $fileOperation->getAllFilePath(__DIR__);
        self::assertEquals($paths, [__DIR__.'/FileOperationTest.php' => __DIR__.'/FileOperationTest.php']);
    }
}
