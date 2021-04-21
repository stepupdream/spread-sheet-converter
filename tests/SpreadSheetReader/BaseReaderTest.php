<?php

namespace StepUpDream\SpreadSheetConverter\Test\SpreadSheetReader;

use Illuminate\Support\Facades\Config;
use Mockery;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers\BaseReader;
use StepUpDream\SpreadSheetConverter\Test\TestCase;

/**
 * Class BaseReaderTest
 *
 * @package StepUpDream\SpreadSheetConverter\Test\SpreadSheetReader
 */
class BaseReaderTest extends TestCase
{
    public function testRead()
    {
        $sheetValues = [
            'sheet_title1' => [
                ['TableName' => 'characters', 'TableDescription' => 'CharacterData', '' => '', 'ColumnName' => 'id', 'ColumnDescription' => 'id'],
                ['TableName' => '', 'TableDescription' => '', '' => '', 'ColumnName' => 'name', 'ColumnDescription' => 'name'],
            ],
            'sheet_title2' => [
                ['TableName' => 'characters2', 'TableDescription' => 'CharacterData2', '' => '', 'ColumnName' => 'id', 'ColumnDescription' => 'id'],
                ['TableName' => '', 'TableDescription' => '', '' => '', 'ColumnName' => 'name2', 'ColumnDescription' => 'name2'],
            ],
        ];
        
        Config::set('spread_sheet.credentials_path', __DIR__.'/credentials.json');
        
        $mock = Mockery::mock(BaseReader::class)->makePartial();
        $mock->shouldReceive('readFromGoogleServiceSheet')
            ->andReturn($sheetValues);
        
        $response = $mock->read('sheet_id');
        self::assertEquals($response, $sheetValues);
        
        $response = $mock->read('sheet_id', 'sheet_title1');
        self::assertEquals($response, $sheetValues['sheet_title1']);
    }
    
}