<?php

namespace StepUpDream\SpreadSheetConverter\Test\DefinitionDocument\Creators;

use Illuminate\Support\Facades\Config;
use Mockery;
use ReflectionClass;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\Api;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ApiAttribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\SubAttribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports\Facades\SpreadSheetReader;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports\SheetOperation;
use StepUpDream\SpreadSheetConverter\Test\TestCase;

/**
 * Class HttpTest
 *
 * @package StepUpDream\SpreadSheetConverter\Test\DefinitionDocument\Creators
 */
class HttpTest extends TestCase
{
    /**
     * @var array
     */
    protected $sheetValues = [
        'sheet_title1' => [
            [
                'ApiName'           => 'Get',
                'HttpDescription'   => 'Get User',
                'HttpMethod'        => 'post',
                ''                  => '',
                'ColumnType'        => 'Request',
                'ColumnName'        => 'target_id',
                'ColumnDescription' => 'target id',
                'DataType'          => 'int',
                'DefaultValue'      => 1,
                'RequestRule'       => 'required|min:1',
            ],
            [
                'ApiName'           => '',
                'HttpDescription'   => '',
                'HttpMethod'        => '',
                ''                  => '',
                'ColumnType'        => 'Response',
                'ColumnName'        => 'user',
                'ColumnDescription' => 'user data',
                'DataType'          => 'string',
                'DefaultValue'      => '',
                'RequestRule'       => '',
            ],
        ],
        'sheet_title2' => [
            [
                'ApiName'           => 'Get2',
                'HttpDescription'   => 'Get User2',
                'HttpMethod'        => 'post',
                ''                  => '',
                'ColumnType'        => 'Request',
                'ColumnName'        => '',
                'ColumnDescription' => '',
                'DataType'          => '',
                'DefaultValue'      => '',
                'RequestRule'       => '',
            ],
            [
                'ApiName'           => '',
                'HttpDescription'   => '',
                'HttpMethod'        => '',
                ''                  => '',
                'ColumnType'        => 'Response',
                'ColumnName'        => 'user',
                'ColumnDescription' => 'user data',
                'DataType'          => 'string',
                'DefaultValue'      => '',
                'RequestRule'       => '',
            ],
        ],
        'RequestRule'  => [
            [
                'ruleDataType' => 'int',
                'ruleMessage'  => 'int message',
            ],
            [
                'ruleDataType' => 'required',
                'ruleMessage'  => 'required message',
            ],
        ],
    ];
    
    public function testRun()
    {
        Config::set('spread_sheet.request_rule_sheet_name', 'RequestRule');
        Config::set('spread_sheet.request_rule_column_name', 'RequestRule');
        SpreadSheetReader::shouldReceive('read')->andReturn($this->sheetValues, $this->sheetValues['RequestRule']);
        SpreadSheetReader::shouldReceive('verifyDataTypeDetail')->andReturn();
        $sheetOperation = $this->app->make(SheetOperation::class);
        $fileOperation = $this->app->make(FileOperation::class);
        $mock = Mockery::mock(Api::class, [$fileOperation, $sheetOperation])->makePartial();
        $mock->shouldReceive('createDefinitionDocument')->andReturn();
        
        $mock->run('Api', 'api', 'sheet_id', base_path('definition_document/database/master_data'));
    }
    
    public function testConvertSheetData()
    {
        Config::set('spread_sheet.request_rule_sheet_name', 'RequestRule');
        Config::set('spread_sheet.request_rule_column_name', 'RequestRule');
        $table = $this->app->make(Api::class);
        $reflection = new ReflectionClass($table);
        $method = $reflection->getMethod('convertApiSheetData');
        $method->setAccessible(true);
        $response = $method->invokeArgs($table, [$this->sheetValues['sheet_title1'], $this->sheetValues['RequestRule'], 'Api', 'sheet_title1']);
        
        $apiAttribute = new ApiAttribute('Api', 'sheet_title1');
        $apiAttribute->setAttributes('Get', 'ApiName');
        $apiAttribute->setAttributes('Get User', 'HttpDescription');
        $apiAttribute->setAttributes('post', 'HttpMethod');
        $apiAttribute->setMainKeyName('Get');
        $subAttribute = new SubAttribute();
        $subAttribute->setAttributes('Request', 'ColumnType');
        $subAttribute->setAttributes('target_id', 'ColumnName');
        $subAttribute->setAttributes('target id', 'ColumnDescription');
        $subAttribute->setAttributes('int', 'DataType');
        $subAttribute->setAttributes('1', 'DefaultValue');
        $subAttribute->setAttributes('required|min:1', 'RequestRule');
        $subAttribute->setRuleMessage("{'required': 'required message'}");
        $subAttribute->setMainKeyName('Request');
        $apiAttribute->setRequestAttributes([$subAttribute]);
        $subAttribute2 = new SubAttribute();
        $subAttribute2->setAttributes('Response', 'ColumnType');
        $subAttribute2->setAttributes('user', 'ColumnName');
        $subAttribute2->setAttributes('user data', 'ColumnDescription');
        $subAttribute2->setAttributes('string', 'DataType');
        $subAttribute2->setAttributes('', 'DefaultValue');
        $subAttribute2->setAttributes('', 'RequestRule');
        $subAttribute2->setRuleMessage('');
        $subAttribute2->setMainKeyName('Response');
        $apiAttribute->setResponseAttributes([$subAttribute2]);
        
        self::assertEquals($response, [$apiAttribute]);
    }
}