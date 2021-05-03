<?php

namespace StepUpDream\SpreadSheetConverter\Test\SpreadSheetReader;

use Illuminate\Support\Facades\Config;
use Mockery;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers\SpreadSheetReader;
use StepUpDream\SpreadSheetConverter\Test\TestCase;

/**
 * Class SpreadSheetReaderTest
 *
 * @package StepUpDream\SpreadSheetConverter\Test\SpreadSheetReader
 */
class SpreadSheetReaderTest extends TestCase
{
    /**
     * @test
     */
    public function read(): void
    {
        $sheetValues = [
            'sheet_title1' => [
                ['TableName' => 'characters', 'TableDescription' => 'CharacterData', 'ColumnName' => 'id', 'ColumnDescription' => 'id'],
                ['TableName' => '', 'TableDescription' => '', 'ColumnName' => 'name', 'ColumnDescription' => 'name'],
            ],
            'sheet_title2' => [
                ['TableName' => 'characters2', 'TableDescription' => 'CharacterData2', 'ColumnName' => 'id', 'ColumnDescription' => 'id'],
                ['TableName' => '', 'TableDescription' => '', 'ColumnName' => 'name2', 'ColumnDescription' => 'name2'],
            ],
        ];
        
        Config::set('step_up_dream.spread_sheet_converter.credentials_path', __DIR__.'/credentials.json');
        
        $spreadSheetReaderMock = Mockery::mock(SpreadSheetReader::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $spreadSheetReaderMock->shouldReceive('readFromGoogleServiceSheet')->andReturn($sheetValues);
        
        $response = $spreadSheetReaderMock->read('sheet_id');
        self::assertEquals($response, $sheetValues);
        
        $response = $spreadSheetReaderMock->read('sheet_id', 'sheet_title1');
        self::assertEquals($response, $sheetValues['sheet_title1']);
    }
    
    /**
     * @test
     */
    public function getTitleArray(): void
    {
        $sheetValues = [
            ['id', 'name'],
            [1, 'sam'],
            [2, 'tom']
        ];
        $response = $this->executePrivateFunction(new SpreadSheetReader(), 'getTitleArray', [$sheetValues, 'sheet_title']);
        $testResult = [
            ['id' => 1, 'name' => 'sam'],
            ['id' => 2, 'name' => 'tom'],
        ];
        
        self::assertEquals($response, $testResult);
    }
    
    /**
     * @test
     */
    public function isAllEmpty(): void
    {
        $values = ['TableName' => '', 'TableDescription' => '', 'ColumnName' => '', 'ColumnDescription' => ''];
        $values2 = ['TableName' => 'hoge', 'TableDescription' => '', 'ColumnName' => '', 'ColumnDescription' => ''];
        
        $spreadSheetReader = new SpreadSheetReader();
        $isAllEmpty = $spreadSheetReader->isAllEmpty($values);
        self::assertTrue($isAllEmpty);
        
        $isAllEmpty2 = $spreadSheetReader->isAllEmpty($values2);
        self::assertFalse($isAllEmpty2);
    }
    
    /**
     * @test
     */
    public function getAttributeKeyName(): void
    {
        $sheet = [
            ['TableName' => 'characters', 'TableDescription' => 'CharacterData', 'ColumnName' => 'id', 'ColumnDescription' => 'id'],
            ['TableName' => '', 'TableDescription' => '', 'ColumnName' => 'name', 'ColumnDescription' => 'name'],
        ];
        
        $spreadSheetReader = new SpreadSheetReader();
        $attributeKeyName = $spreadSheetReader->getAttributeKeyName($sheet, 'ColumnName');
        $testResult = ['ColumnName' => 'ColumnName', 'ColumnDescription' => 'ColumnDescription'];
        
        self::assertEquals($attributeKeyName, $testResult);
    }
    
    /**
     * @test
     */
    public function getParentAttributeKeyName(): void
    {
        $sheet = [
            ['TableName' => 'characters', 'TableDescription' => 'CharacterData', 'ColumnName' => 'id', 'ColumnDescription' => 'id'],
            ['TableName' => '', 'TableDescription' => '', 'ColumnName' => 'name', 'ColumnDescription' => 'name'],
        ];
        
        $spreadSheetReader = new SpreadSheetReader();
        $parentAttributeKeyName = $spreadSheetReader->getParentAttributeKeyName($sheet, 'ColumnName');
        $testResult = ['TableName' => 'TableName', 'TableDescription' => 'TableDescription'];
        
        self::assertEquals($parentAttributeKeyName, $testResult);
    }
}
