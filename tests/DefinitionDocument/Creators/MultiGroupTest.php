<?php

namespace StepUpDream\SpreadSheetConverter\Test\DefinitionDocument\Creators;

use Illuminate\Support\Facades\Config;
use Mockery;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\MultiGroup;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers\SpreadSheetReader;
use StepUpDream\SpreadSheetConverter\Test\TestCase;

/**
 * Class MultiGroupTest.
 */
class MultiGroupTest extends TestCase
{
    /**
     * @test
     */
    public function convertSheetData(): void
    {
        $sheetValues = [
            [
                'ApiName'           => 'Get',
                'ApiDescription'    => 'get',
                'GroupType'         => 'Request',
                'ColumnName'        => 'id',
                'ColumnDescription' => 'id',
                'DataType'          => 'int',
            ],
            [
                'ApiName'           => '',
                'ApiDescription'    => '',
                'GroupType'         => '',
                'ColumnName'        => 'name',
                'ColumnDescription' => 'name',
                'DataType'          => 'string',
            ],
            [
                'ApiName'           => '',
                'ApiDescription'    => '',
                'GroupType'         => 'Response',
                'ColumnName'        => 'id',
                'ColumnDescription' => 'id',
                'DataType'          => 'int',
            ],
            [
                'TableName'         => '',
                'TableDescription'  => '',
                'ColumnName'        => '',
                'ColumnDescription' => '',
                'DataType'          => '',
            ],
            [
                'TableName'         => '',
                'TableDescription'  => '',
                'ColumnName'        => '',
                'ColumnDescription' => '',
                'DataType'          => '',
            ],
            [
                'ApiName'           => 'Get2',
                'ApiDescription'    => 'get2',
                'GroupType'         => 'Request',
                'ColumnName'        => '',
                'ColumnDescription' => '',
                'DataType'          => '',
            ],
            [
                'ApiName'           => '',
                'ApiDescription'    => '',
                'GroupType'         => 'Response',
                'ColumnName'        => 'id',
                'ColumnDescription' => 'id',
                'DataType'          => 'int',
            ],
            [
                'ApiName'           => '',
                'ApiDescription'    => '',
                'GroupType'         => '',
                'ColumnName'        => 'name',
                'ColumnDescription' => 'name',
                'DataType'          => 'string',
            ],
        ];

        // Group1
        $parentAttribute = new ParentAttribute('Api', 'sheetName');
        $parentAttribute->setParentAttributeDetails('Get', 'ApiName');
        $parentAttribute->setParentAttributeDetails('get', 'ApiDescription');
        $attribute = new Attribute();
        $attribute->setAttributeDetails('id', 'ColumnName');
        $attribute->setAttributeDetails('id', 'ColumnDescription');
        $attribute->setAttributeDetails('int', 'DataType');
        $attribute2 = new Attribute();
        $attribute2->setAttributeDetails('name', 'ColumnName');
        $attribute2->setAttributeDetails('name', 'ColumnDescription');
        $attribute2->setAttributeDetails('string', 'DataType');
        $parentAttribute->setAttributesGroup([$attribute, $attribute2], 'Request');
        $attribute3 = new Attribute();
        $attribute3->setAttributeDetails('id', 'ColumnName');
        $attribute3->setAttributeDetails('id', 'ColumnDescription');
        $attribute3->setAttributeDetails('int', 'DataType');
        $parentAttribute->setAttributesGroup([$attribute3], 'Response');

        // Group2
        $parentAttribute2 = new ParentAttribute('Api', 'sheetName');
        $parentAttribute2->setParentAttributeDetails('Get2', 'ApiName');
        $parentAttribute2->setParentAttributeDetails('get2', 'ApiDescription');
        $parentAttribute2->setAttributesGroup([], 'Request');
        $attribute4 = new Attribute();
        $attribute4->setAttributeDetails('id', 'ColumnName');
        $attribute4->setAttributeDetails('id', 'ColumnDescription');
        $attribute4->setAttributeDetails('int', 'DataType');
        $attribute5 = new Attribute();
        $attribute5->setAttributeDetails('name', 'ColumnName');
        $attribute5->setAttributeDetails('name', 'ColumnDescription');
        $attribute5->setAttributeDetails('string', 'DataType');
        $parentAttribute2->setAttributesGroup([$attribute4, $attribute5], 'Response');

        $argument = [
            'category_name'               => 'MasterData',
            'use_blade'                   => 'master_data',
            'sheet_id'                    => 'sheet_id',
            'output_directory_path'       => base_path('definition_document/database/master_data'),
            'separation_key'              => 'GroupType',
            'attribute_group_column_name' => 'GroupType',
        ];
        $fileOperation = $this->app->make(FileOperation::class);
        $spreadSheetReader = $this->app->make(SpreadSheetReader::class);

        // createRuleMessage includes Google processing, so cut it out as a separate test
        $multiGroup = Mockery::mock(MultiGroup::class, [$fileOperation, $spreadSheetReader, $argument])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $multiGroup->shouldReceive('createRuleMessage')->andReturn();

        /* @see MultiGroup::convertSheetData() */
        $response = $this->executePrivateFunction($multiGroup, 'convertSheetData', [$sheetValues, 'Api', 'sheetName']);

        self::assertEquals($response, [$parentAttribute, $parentAttribute2]);
    }

    /**
     * @test
     */
    public function createRuleMessage(): void
    {
        Config::set('step_up_dream.spread_sheet_converter.request_rule_sheet_name', 'RequestRule');
        Config::set('step_up_dream.spread_sheet_converter.request_rule_column_name', 'RequestRule');

        $sheetValues = [
            [
                'ApiName'           => 'Get',
                'ApiDescription'    => 'get',
                'GroupType'         => 'Request',
                'ColumnName'        => 'id',
                'ColumnDescription' => 'id',
                'DataType'          => 'int',
                'RequestRule'       => 'required',
            ],
            [
                'ApiName'           => 'Get',
                'ApiDescription'    => 'get',
                'GroupType'         => 'Request',
                'ColumnName'        => 'id',
                'ColumnDescription' => 'id',
                'DataType'          => 'int',
                'RequestRule'       => 'required | int',
            ],
            [
                'ApiName'           => '',
                'ApiDescription'    => '',
                'GroupType'         => 'Response',
                'ColumnName'        => 'name',
                'ColumnDescription' => 'name',
                'DataType'          => 'string',
                'RequestRule'       => '',
            ],
            [
                'ApiName'           => '',
                'ApiDescription'    => '',
                'GroupType'         => 'Response',
                'ColumnName'        => 'name',
                'ColumnDescription' => 'name',
                'DataType'          => 'string',
            ],
        ];

        $ruleSheetValues = [
            [
                'ruleDataType' => 'int',
                'ruleMessage'  => 'int message',
            ],
            [
                'ruleDataType' => 'required',
                'ruleMessage'  => 'required message',
            ],
        ];

        $argument = [
            'category_name'               => 'MasterData',
            'use_blade'                   => 'master_data',
            'sheet_id'                    => 'sheet_id',
            'output_directory_path'       => base_path('definition_document/database/master_data'),
            'separation_key'              => 'GroupType',
            'attribute_group_column_name' => 'GroupType',
        ];
        $spreadSheetReaderMock = Mockery::mock(SpreadSheetReader::class)->makePartial();
        $spreadSheetReaderMock->shouldReceive('read')->andReturn($ruleSheetValues);
        $fileOperation = $this->app->make(FileOperation::class);
        $multiGroup = $this->app->make(MultiGroup::class, [
            'fileOperation'     => $fileOperation,
            'spreadSheetReader' => $spreadSheetReaderMock,
            'readSpreadSheet'   => $argument,
        ]);

        /* @see MultiGroup::createRuleMessage() */
        $response = $this->executePrivateFunction($multiGroup, 'createRuleMessage', [$sheetValues, 0]);
        self::assertEquals("{'required': 'required message'}", $response);
        $response2 = $this->executePrivateFunction($multiGroup, 'createRuleMessage', [$sheetValues, 1]);
        self::assertEquals("{'required': 'required message', 'int': 'int message'}", $response2);
        $response3 = $this->executePrivateFunction($multiGroup, 'createRuleMessage', [$sheetValues, 2]);
        self::assertEquals('', $response3);
        $response4 = $this->executePrivateFunction($multiGroup, 'createRuleMessage', [$sheetValues, 3]);
        self::assertEquals('', $response4);
    }
}
