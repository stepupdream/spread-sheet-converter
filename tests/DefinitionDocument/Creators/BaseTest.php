<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\Test\DefinitionDocument\Creators;

use Mockery;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\Base;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers\SpreadSheetReader;
use StepUpDream\SpreadSheetConverter\Test\TestCase;

class BaseTest extends TestCase
{
    /**
     * @test
     */
    public function mainRun(): void
    {
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
            'output_directory_path'       => base_path('definition_document/database/master_data'),
            'separation_key'              => 'ColumnName',
            'attribute_group_column_name' => null,
        ];

        $baseMock = Mockery::mock(Base::class, [$fileOperationMock, $spreadSheetReaderMock, $argument])->makePartial();
        $baseMock->allows('createDefinitionDocument')->andReturns();

        /** @var Base $baseMock */
        $baseMock->run(null);
    }
}
