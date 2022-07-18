<?php

namespace StepUpDream\SpreadSheetConverter\Test\DefinitionDocument\Creators;

use Mockery;
use ReflectionClass;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\Table;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\SubAttribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports\Facades\SpreadSheetReader;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports\SheetOperation;
use StepUpDream\SpreadSheetConverter\Test\TestCase;

/**
 * Class TableTest
 *
 * @package StepUpDream\SpreadSheetConverter\Test\SpreadSheetReaders
 */
class TableTest extends TestCase
{
    public function testRun(): void
    {
        $sheetValues = [
            'sheet_title1' => [
                [
                    'TableName'         => 'characters',
                    'TableDescription'  => 'CharacterData',
                    ''                  => '',
                    'ColumnName'        => 'id',
                    'ColumnDescription' => 'id',
                    'DataType'          => 'int'
                ],
                [
                    'TableName'         => '',
                    'TableDescription'  => '',
                    ''                  => '',
                    'ColumnName'        => 'name',
                    'ColumnDescription' => 'name',
                    'DataType'          => 'string'
                ],
            ],
            'sheet_title2' => [
                [
                    'TableName'         => 'characters2',
                    'TableDescription'  => 'CharacterData2',
                    ''                  => '',
                    'ColumnName'        => 'id',
                    'ColumnDescription' => 'id',
                    'DataType'          => 'int'
                ],
                [
                    'TableName'         => '',
                    'TableDescription'  => '',
                    ''                  => '',
                    'ColumnName'        => 'name2',
                    'ColumnDescription' => 'name2',
                    'DataType'          => 'string'
                ],
            ],
        ];
        
        SpreadSheetReader::shouldReceive('read')->andReturn($sheetValues);
        SpreadSheetReader::shouldReceive('verifyDataTypeDetail')->andReturn();
        $sheetOperation = $this->app->make(SheetOperation::class);
        $fileOperation = $this->app->make(FileOperation::class);
        $mock = Mockery::mock(Table::class, [$fileOperation, $sheetOperation])->makePartial();
        $mock->shouldReceive('createDefinitionDocument')->andReturn();
        
        $mock->run('MasterData', 'master_data', 'sheet_id', base_path('definition_document/database/master_data'));
    }
    
    public function testConvertSheetData(): void
    {
        $sheetValues = [
            [
                'TableName'         => 'characters',
                'TableDescription'  => 'CharacterData',
                ''                  => '',
                'ColumnName'        => 'id',
                'ColumnDescription' => 'id',
                'DataType'          => 'int'
            ],
            [
                'TableName'         => '',
                'TableDescription'  => '',
                ''                  => '',
                'ColumnName'        => 'name',
                'ColumnDescription' => 'name',
                'DataType'          => 'string'
            ],
            [
                'TableName'         => '',
                'TableDescription'  => '',
                ''                  => '',
                'ColumnName'        => '',
                'ColumnDescription' => '',
                'DataType'          => ''
            ],
            [
                'TableName'         => 'equipments',
                'TableDescription'  => 'EquipmentData',
                ''                  => '',
                'ColumnName'        => 'id',
                'ColumnDescription' => 'id',
                'DataType'          => 'int'
            ],
        ];
        
        $table = $this->app->make(Table::class);
        $reflection = new ReflectionClass($table);
        $method = $reflection->getMethod('convertSheetData');
        $method->setAccessible(true);
        $response = $method->invokeArgs($table, [$sheetValues, 'MasterData', 'sheetName']);
        
        // Attribute1
        $attribute = new Attribute('MasterData', 'sheetName');
        $attribute->setAttributes('characters', 'TableName');
        $attribute->setAttributes('CharacterData', 'TableDescription');
        $attribute->setMainKeyName('characters');
        $subAttribute = new SubAttribute();
        $subAttribute->setAttributes('id', 'ColumnName');
        $subAttribute->setAttributes('id', 'ColumnDescription');
        $subAttribute->setAttributes('int', 'DataType');
        $subAttribute->setMainKeyName('id');
        $subAttribute2 = new SubAttribute();
        $subAttribute2->setAttributes('name', 'ColumnName');
        $subAttribute2->setAttributes('name', 'ColumnDescription');
        $subAttribute2->setAttributes('string', 'DataType');
        $subAttribute2->setMainKeyName('name');
        $attribute->setSubAttributes([$subAttribute, $subAttribute2]);
        
        // Attribute2
        $attribute2 = new Attribute('MasterData', 'sheetName');
        $attribute2->setAttributes('equipments', 'TableName');
        $attribute2->setAttributes('EquipmentData', 'TableDescription');
        $attribute2->setMainKeyName('equipments');
        $subAttribute = new SubAttribute();
        $subAttribute->setAttributes('id', 'ColumnName');
        $subAttribute->setAttributes('id', 'ColumnDescription');
        $subAttribute->setAttributes('int', 'DataType');
        $subAttribute->setMainKeyName('id');
        $attribute2->setSubAttributes([$subAttribute]);
        
        self::assertEquals($response, [$attribute, $attribute2]);
    }
}
