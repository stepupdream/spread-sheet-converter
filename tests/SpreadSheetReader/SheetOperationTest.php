<?php

namespace StepUpDream\SpreadSheetConverter\Test\SpreadSheetReader;

use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports\SheetOperation;
use StepUpDream\SpreadSheetConverter\Test\TestCase;

/**
 * Class SheetOperationTest
 *
 * @package StepUpDream\SpreadSheetConverter\Test\SpreadSheetReader
 */
class SheetOperationTest extends TestCase
{
    public function testGetTitleArray(): void
    {
        $sheetValues = [
            ['id', 'name'],
            [1, 'sam'],
            [2, 'tom']
        ];
        
        $sheetOperation = new SheetOperation();
        $sheetData = $sheetOperation->getTitleArray($sheetValues, 'sheet_title');
        $testResult = [
            ['id' => 1, 'name' => 'sam'],
            ['id' => 2, 'name' => 'tom'],
        ];
        
        self::assertEquals($sheetData, $testResult);
    }
    
    public function testGetMainAttributeKeyName(): void
    {
        $sheet = [
            ['TableName' => 'characters', 'TableDescription' => 'CharacterData', '' => '', 'ColumnName' => 'id', 'ColumnDescription' => 'id'],
            ['TableName' => '', 'TableDescription' => '', '' => '', 'ColumnName' => 'name', 'ColumnDescription' => 'name'],
        ];
        
        $sheetOperation = new SheetOperation();
        $mainAttributeKeyName = $sheetOperation->getMainAttributeKeyName($sheet);
        $testResult = ['TableName' => 'TableName', 'TableDescription' => 'TableDescription'];
        
        self::assertEquals($mainAttributeKeyName, $testResult);
    }
    
    public function testGetSubAttributeKeyName(): void
    {
        $sheet = [
            ['TableName' => 'characters', 'TableDescription' => 'CharacterData', '' => '', 'ColumnName' => 'id', 'ColumnDescription' => 'id'],
            ['TableName' => '', 'TableDescription' => '', '' => '', 'ColumnName' => 'name', 'ColumnDescription' => 'name'],
        ];
        
        $sheetOperation = new SheetOperation();
        $subAttributeKeyName = $sheetOperation->getSubAttributeKeyName($sheet);
        $testResult = ['ColumnName' => 'ColumnName', 'ColumnDescription' => 'ColumnDescription'];
        
        self::assertEquals($subAttributeKeyName, $testResult);
    }
    
    public function testIsAllEmpty(): void
    {
        $values = ['TableName' => '', 'TableDescription' => '', '' => '', 'ColumnName' => '', 'ColumnDescription' => ''];
        $values2 = ['TableName' => 'hoge', 'TableDescription' => '', '' => '', 'ColumnName' => '', 'ColumnDescription' => ''];
        
        $sheetOperation = new SheetOperation();
        $isAllEmpty = $sheetOperation->isAllEmpty($values);
        self::assertTrue($isAllEmpty);
        
        $isAllEmpty2 = $sheetOperation->isAllEmpty($values2);
        self::assertFalse($isAllEmpty2);
    }
}