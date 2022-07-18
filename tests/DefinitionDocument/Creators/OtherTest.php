<?php

namespace StepUpDream\SpreadSheetConverter\Test\DefinitionDocument\Creators;

use ReflectionClass;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\Other;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\SubAttribute;
use StepUpDream\SpreadSheetConverter\Test\TestCase;

/**
 * Class OtherTest
 *
 * @package StepUpDream\SpreadSheetConverter\Test\SpreadSheetReaders
 */
class OtherTest extends TestCase
{
    public function testConvertSheetData()
    {
        $sheetValues = [
            [
                'ColumnName'        => 'id',
                'ColumnDescription' => 'id',
                'DataType'          => 'int'
            ],
            [
                'ColumnName'        => 'name',
                'ColumnDescription' => 'name',
                'DataType'          => 'string'
            ],
            [
                'TableName'         => '',
                'TableDescription'  => '',
                'ColumnName'        => '',
                'ColumnDescription' => '',
                'DataType'          => ''
            ],
            [
                'ColumnName'        => 'id',
                'ColumnDescription' => 'id',
                'DataType'          => 'int'
            ],
        ];
        
        $table = $this->app->make(Other::class);
        $reflection = new ReflectionClass($table);
        $method = $reflection->getMethod('convertSheetData');
        $method->setAccessible(true);
        $response = $method->invokeArgs($table, [$sheetValues, 'Other', 'sheetName']);
        
        // Attribute1
        $attribute = new Attribute('Other', 'sheetName');
        $attribute->setMainKeyName('sheetName');
        $subAttribute = new SubAttribute();
        $subAttribute->setAttributes('id', 'ColumnName');
        $subAttribute->setAttributes('id', 'ColumnDescription');
        $subAttribute->setAttributes('int', 'DataType');
        $subAttribute2 = new SubAttribute();
        $subAttribute2->setAttributes('name', 'ColumnName');
        $subAttribute2->setAttributes('name', 'ColumnDescription');
        $subAttribute2->setAttributes('string', 'DataType');
        $attribute->setSubAttributes([$subAttribute, $subAttribute2]);
        
        // Attribute2
        $attribute2 = new Attribute('Other', 'sheetName');
        $attribute2->setMainKeyName('sheetName');
        $subAttribute = new SubAttribute();
        $subAttribute->setAttributes('id', 'ColumnName');
        $subAttribute->setAttributes('id', 'ColumnDescription');
        $subAttribute->setAttributes('int', 'DataType');
        $attribute2->setSubAttributes([$subAttribute]);
        
        self::assertEquals($response, [$attribute, $attribute2]);
    }
}
