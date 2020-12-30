<?php

namespace StepUpDream\SpreadSheetConverter\Test;

use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports\SheetOperation;

/**
 * Class SheetOperationTest
 *
 * @package StepUpDream\SpreadSheetConverter\Test
 */
class SheetOperationTest extends TestCase
{
    public function testGetTitleArray()
    {
        $sheet_values = [
            ['id', 'name'],
            [1, 'sam'],
            [2, 'tom']
        ];
        
        $sheet_operation = new SheetOperation();
        $sheet_data = $sheet_operation->getTitleArray($sheet_values, 'sheet_title');
        $test_result = [
            ['id' => 1, 'name' => 'sam'],
            ['id' => 2, 'name' => 'tom'],
        ];
        
        self::assertEquals($sheet_data, $test_result);
    }
    
    public function testGetMainAttributeKeyName()
    {
        $sheet = [
            ['TableName' => 'characters', 'TableDescription' => 'CharacterData', '' => '', 'ColumnName' => 'id', 'ColumnDescription' => 'id'],
            ['TableName' => '', 'TableDescription' => '', '' => '', 'ColumnName' => 'name', 'ColumnDescription' => 'name'],
        ];
        
        $sheet_operation = new SheetOperation();
        $main_attribute_key_name = $sheet_operation->getMainAttributeKeyName($sheet);
        $test_result = ['TableName', 'TableDescription'];
        
        self::assertEquals($main_attribute_key_name, $test_result);
    }
    
    public function testGetSubAttributeKeyName()
    {
        $sheet = [
            ['TableName' => 'characters', 'TableDescription' => 'CharacterData', '' => '', 'ColumnName' => 'id', 'ColumnDescription' => 'id'],
            ['TableName' => '', 'TableDescription' => '', '' => '', 'ColumnName' => 'name', 'ColumnDescription' => 'name'],
        ];
        
        $sheet_operation = new SheetOperation();
        $sub_attribute_key_name = $sheet_operation->getSubAttributeKeyName($sheet);
        $test_result = ['ColumnName' => 'ColumnName', 'ColumnDescription' => 'ColumnDescription'];
        
        self::assertEquals($sub_attribute_key_name, $test_result);
    }
}