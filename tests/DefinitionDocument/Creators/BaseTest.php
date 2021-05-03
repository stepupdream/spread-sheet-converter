<?php

namespace StepUpDream\SpreadSheetConverter\Test\DefinitionDocument\Creators;

use Mockery;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\Base;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\SingleGroup;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers\SpreadSheetReader;
use StepUpDream\SpreadSheetConverter\Test\TestCase;

/**
 * Class BaseTest
 *
 * @package StepUpDream\SpreadSheetConverter\Test\SpreadSheetReaders
 */
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
                    'DataType'          => 'int'
                ],
                [
                    'TableName'         => '',
                    'TableDescription'  => '',
                    'ColumnName'        => 'name',
                    'ColumnDescription' => 'name',
                    'DataType'          => 'string'
                ],
            ],
            'sheet_title2' => [
                [
                    'TableName'         => 'characters2',
                    'TableDescription'  => 'CharacterData2',
                    'ColumnName'        => 'id',
                    'ColumnDescription' => 'id',
                    'DataType'          => 'int'
                ],
                [
                    'TableName'         => '',
                    'TableDescription'  => '',
                    'ColumnName'        => 'name2',
                    'ColumnDescription' => 'name2',
                    'DataType'          => 'string'
                ],
            ],
        ];
        
        $spreadSheetReaderMock = Mockery::mock(SpreadSheetReader::class)->makePartial();
        $spreadSheetReaderMock->shouldReceive('read')->andReturn($sheetValues);
        $fileOperationMock = $this->app->make(FileOperation::class);
        $argument = [
            'category_name'               => 'MasterData',
            'use_blade'                   => 'master_data',
            'sheet_id'                    => 'sheet_id',
            'output_directory_path'       => base_path('definition_document/database/master_data'),
            'separation_key'              => 'ColumnName',
            'attribute_group_column_name' => null,
        ];
        
        $baseMock = Mockery::mock(Base::class, [$fileOperationMock, $spreadSheetReaderMock, $argument])->makePartial();
        $baseMock->shouldReceive('createDefinitionDocument')->andReturn();
        
        /** @var Base $baseMock */
        $baseMock->run();
    }
    
    /**
     * @test
     */
    public function isReadSkip(): void
    {
        $parentAttribute = new ParentAttribute('spreadsheetCategoryName', 'sheetName');
        $parentAttribute->setParentAttributeDetails('Users', 'tableName');
        $parentAttribute->setParentAttributeDetails('user_db', 'connection');
        $argument = [
            'category_name'               => 'MasterData',
            'use_blade'                   => 'master_data',
            'sheet_id'                    => 'sheet_id',
            'output_directory_path'       => base_path('definition_document/database/master_data'),
            'separation_key'              => 'ColumnName',
            'attribute_group_column_name' => null,
        ];
        $fileOperation = $this->app->make(FileOperation::class);
        $spreadSheetReader = $this->app->make(SpreadSheetReader::class);
        
        /* @see SingleGroup::isReadSkip() */
        $singleGroup = new SingleGroup($fileOperation, $spreadSheetReader, $argument);
        $response = $this->executePrivateFunction($singleGroup, 'isReadSkip', [$parentAttribute, 'users']);
        self::assertFalse($response);
        
        $response = $this->executePrivateFunction($singleGroup, 'isReadSkip', [$parentAttribute, 'notUsers']);
        self::assertTrue($response);
        
        $response = $this->executePrivateFunction($singleGroup, 'isReadSkip', [$parentAttribute, null]);
        self::assertFalse($response);
    }
}
