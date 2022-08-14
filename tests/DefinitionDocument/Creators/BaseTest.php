<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\Test\DefinitionDocument\Creators;

use Illuminate\Console\OutputStyle;
use Illuminate\Filesystem\Filesystem;
use Mockery;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\Base;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers\SpreadSheetReader;
use StepUpDream\SpreadSheetConverter\Test\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class BaseTest extends TestCase
{
    /**
     * @test
     */
    public function mainRun(): void
    {
        $this->resultReset();

        $sheetValues = [
            'sheet_title1' => [
                [
                    'TableName'         => 'characters',
                    'TableDescription'  => 'CharacterData',
                    'ColumnName'        => 'id',
                    'ColumnDescription' => 'id',
                    'DataType'          => 'int',
                ],
                [
                    'TableName'         => '',
                    'TableDescription'  => '',
                    'ColumnName'        => 'name',
                    'ColumnDescription' => 'name',
                    'DataType'          => 'string',
                ],
            ],
            'sheet_title2' => [
                [
                    'TableName'         => 'characters2',
                    'TableDescription'  => 'CharacterData2',
                    'ColumnName'        => 'id',
                    'ColumnDescription' => 'id',
                    'DataType'          => 'int',
                ],
                [
                    'TableName'         => '',
                    'TableDescription'  => '',
                    'ColumnName'        => 'name2',
                    'ColumnDescription' => 'name2',
                    'DataType'          => 'string',
                ],
            ],
        ];

        $spreadSheetReaderMock = Mockery::mock(SpreadSheetReader::class)->makePartial();
        $spreadSheetReaderMock->allows('read')->andReturns($sheetValues);
        $spreadSheetReaderMock->allows('spreadSheetTitle')->andReturns('title');
        $fileOperationMock = $this->app->make(FileOperation::class);
        $argument = [
            'category_tag'                => 'MasterData',
            'use_blade'                   => 'master_data',
            'sheet_id'                    => 'sheet_id',
            'output_directory_path'       => __DIR__.'/../Sample/Output',
            'definition_directory_path'   => __DIR__.'/../Sample/Definition',
            'separation_key'              => 'ColumnName',
            'attribute_group_column_name' => null,
        ];

        $baseMock = Mockery::mock(Base::class, [$fileOperationMock, $spreadSheetReaderMock, $argument])->makePartial();
        $methodName = 'loadBladeFile';
        $baseMock->shouldAllowMockingProtectedMethods()->allows($methodName)->andReturns("sample\n");

        /** @var Base $baseMock */
        $bufferedOutput = new BufferedOutput();
        $style = new OutputStyle(new ArrayInput([]), $bufferedOutput);

        // Test output path.
        $outputData1 = __DIR__.'/../Sample/Output/SheetTitle1/characters.yml';
        $outputData2 = __DIR__.'/../Sample/Output/SheetTitle2/characters2.yml';

        // Skip test.
        $baseMock->setOutput($style)->run('characters');
        self::assertFileExists($outputData1);
        self::assertFileDoesNotExist($outputData2);
        self::assertFileExists(__DIR__.'/../Sample/Definition/.gitkeep');

        // First time.
        // If the process is executed when there is no data, a file is generated at the output destination.
        $this->resultReset();
        $baseMock->setOutput($style)->run(null);
        self::assertFileEquals($outputData1, __DIR__.'/../Answer/characters.yml');
        self::assertFileEquals($outputData2, __DIR__.'/../Answer/characters2.yml');
        self::assertFileExists(__DIR__.'/../Sample/Definition/.gitkeep');

        // Second run.
        // Move the output file to the official folder.（Prepare the file before testing）
        // If the official folder and the data to be output are the same, do not re-output.
        $fileOperationMock->createFile("sample\n", __DIR__.'/../Sample/Definition/SheetTitle1/characters.yml');
        $fileOperationMock->createFile("sample\n", __DIR__.'/../Sample/Definition/SheetTitle2/characters2.yml');
        $this->refreshOutputData();
        $baseMock->setOutput($style)->run(null);
        self::assertFileDoesNotExist($outputData1);
        self::assertFileDoesNotExist($outputData2);

        $this->resultReset();
    }

    /**
     * Delete output data.
     *
     * @return void
     */
    protected function refreshOutputData(): void
    {
        $filesystem = $this->app->make(Filesystem::class);
        $filesystem->deleteDirectory(__DIR__.'/../Sample/Output');
    }
}
