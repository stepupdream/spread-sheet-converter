<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\Test\DefinitionDocument\Creators;

use Illuminate\Console\OutputStyle;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Mockery;
use StepUpDream\DreamAbilitySupport\Supports\File\FileOperation;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\BladeLoader;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\TwoAreaCreator;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute;
use StepUpDream\SpreadSheetConverter\SpreadSheetService\GoogleService;
use StepUpDream\SpreadSheetConverter\SpreadSheetService\GoogleServiceSheet;
use StepUpDream\SpreadSheetConverter\SpreadSheetService\Readers\SpreadSheetReader;
use StepUpDream\SpreadSheetConverter\Test\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class TwoAreaCreatorTest extends TestCase
{
    private array $mock = [
        'sheetName1'  => [
            [
                'ApiName',
                'ApiDescription',
                'GroupType',
                'ColumnName',
                'ColumnDescription',
                'DataType',
                'RequestRule',
            ],
            [
                'Get',
                'get',
                'Request',
                'id',
                'id',
                'int',
                'required|int',
            ],
            [
                '',
                '',
                '',
                'name',
                'name',
                'string',
                'required',
            ],
            [
                '',
                '',
                'Response',
                'id',
                'id',
                'int',
                '',
            ],
            [
                '',
                '',
                '',
                '',
                '',
                '',
            ],
            [
                'Get2',
                'get2',
                'Request',
                '',
                '',
                '',
                '',
            ],
            [
                '',
                '',
                'Response',
                'id',
                'id',
                'int',
                '',
            ],
            [
                '',
                '',
                '',
                'name',
                'name',
                'string',
                '',
            ],
        ],
        'RequestRule' => [
            [
                'ruleDataType',
                'ruleMessage',
            ],
            [
                'int',
                'int rule',
            ],
            [
                'required',
                'required rule',
            ],
        ],
    ];

    /**
     * @test
     */
    public function twoAreaCreator(): void
    {
        Config::set('stepupdream.spread-sheet-converter.request_rule_sheet_name', 'RequestRule');
        Config::set('stepupdream.spread-sheet-converter.request_rule_column_name', 'RequestRule');

        $googleServiceSheet = new GoogleServiceSheet('spreadSheetTitle', $this->mock);

        $sheetValues = [
            'sheetName1'  => [
                [
                    'ApiName'           => 'Get',
                    'ApiDescription'    => 'get',
                    'GroupType'         => 'Request',
                    'ColumnName'        => 'id',
                    'ColumnDescription' => 'id',
                    'DataType'          => 'int',
                    'RequestRule'       => 'required|int',
                ],
                [
                    'ApiName'           => '',
                    'ApiDescription'    => '',
                    'GroupType'         => '',
                    'ColumnName'        => 'name',
                    'ColumnDescription' => 'name',
                    'DataType'          => 'string',
                    'RequestRule'       => 'required',
                ],
                [
                    'ApiName'           => '',
                    'ApiDescription'    => '',
                    'GroupType'         => 'Response',
                    'ColumnName'        => 'id',
                    'ColumnDescription' => 'id',
                    'DataType'          => 'int',
                    'RequestRule'       => '',
                ],
                [
                    'ApiName'           => '',
                    'ApiDescription'    => '',
                    'GroupType'         => '',
                    'ColumnName'        => '',
                    'ColumnDescription' => '',
                    'DataType'          => '',
                    'RequestRule'       => '',
                ],
                [
                    'ApiName'           => 'Get2',
                    'ApiDescription'    => 'get2',
                    'GroupType'         => 'Request',
                    'ColumnName'        => '',
                    'ColumnDescription' => '',
                    'DataType'          => '',
                    'RequestRule'       => '',
                ],
                [
                    'ApiName'           => '',
                    'ApiDescription'    => '',
                    'GroupType'         => 'Response',
                    'ColumnName'        => 'id',
                    'ColumnDescription' => 'id',
                    'DataType'          => 'int',
                    'RequestRule'       => '',
                ],
                [
                    'ApiName'           => '',
                    'ApiDescription'    => '',
                    'GroupType'         => '',
                    'ColumnName'        => 'name',
                    'ColumnDescription' => 'name',
                    'DataType'          => 'string',
                    'RequestRule'       => '',
                ],
            ],
            'RequestRule' => [
                [
                    'ruleDataType' => 'int',
                    'ruleMessage'  => 'int rule',
                ],
                [
                    'ruleDataType' => 'required',
                    'ruleMessage'  => 'required rule',
                ],
            ],
        ];

        $readSpreadSheet = [
            'category_tag'                => 'TwoArea',
            'use_blade'                   => 'TwoAreaBlade',
            'sheet_id'                    => 'sheet_id',
            'output_directory_path'       => __DIR__.'/TestCode/output',
            'definition_directory_path'   => __DIR__.'/TestCode/definition',
            'separation_key'              => 'GroupType',
            'attribute_group_column_name' => 'GroupType',
            'file_extension'              => 'yml',
        ];
        $fileOperation = $this->app->make(FileOperation::class);
        $googleService = Mockery::mock(GoogleService::class);
        $googleService->allows('readFromGoogleServiceSheet')->andReturns($googleServiceSheet);
        $spreadSheetReader = new SpreadSheetReader($googleService);
        $bladeLoader = Mockery::mock(BladeLoader::class);
        $bladeLoader->allows('loadBladeFile')->andReturns('test');

        // initialization.
        $filesystem = $this->app->make(Filesystem::class);
        $filesystem->deleteDirectory(__DIR__.'/TestCode');

        // sheet read test.
        $readSpreadSheetValue = $spreadSheetReader->read('sheet_id');
        self::assertEquals($readSpreadSheetValue, $sheetValues);

        // command test.
        $bufferedOutput = new BufferedOutput();
        $style = new OutputStyle(new ArrayInput([]), $bufferedOutput);
        $twoAreaCreator = new TwoAreaCreator($fileOperation, $spreadSheetReader, $bladeLoader, $readSpreadSheet);
        $twoAreaCreator->setOutput($style)->run(null);

        $fileContent1 = file_get_contents(__DIR__.'/TestCode/output/SheetName1/Get.yml');
        $fileContent2 = file_get_contents(__DIR__.'/TestCode/output/SheetName1/Get2.yml');
        self::assertEquals('test', $fileContent1);
        self::assertEquals('test', $fileContent2);
        $filesystem->deleteDirectory(__DIR__.'/TestCode');

        // skip test.
        $twoAreaCreator->setOutput($style)->run('Get2');
        self::assertFileDoesNotExist(__DIR__.'/TestCode/output/SheetName1/Get.yml');
        $filesystem->deleteDirectory(__DIR__.'/TestCode');

        // duplicate creation check.
        $fileOperation->createFile('test', __DIR__.'/TestCode/definition/SheetName1/Get.yml');
        $twoAreaCreator->setOutput($style)->run('Get');
        self::assertFileDoesNotExist(__DIR__.'/TestCode/output/SheetName1/Get.yml');
        $filesystem->deleteDirectory(__DIR__.'/TestCode');
    }

    /**
     * @test
     */
    public function convertSheetData(): void
    {
        Config::set('stepupdream.spread-sheet-converter.request_rule_sheet_name', 'RequestRule');
        Config::set('stepupdream.spread-sheet-converter.request_rule_column_name', 'RequestRule');

        $googleServiceSheet = new GoogleServiceSheet('spreadSheetTitle', $this->mock);

        $sheetValues = [
            [
                'ApiName'           => 'Get',
                'ApiDescription'    => 'get',
                'GroupType'         => 'Request',
                'ColumnName'        => 'id',
                'ColumnDescription' => 'id',
                'DataType'          => 'int',
                'RequestRule'       => 'required|int',
            ],
            [
                'ApiName'           => '',
                'ApiDescription'    => '',
                'GroupType'         => '',
                'ColumnName'        => 'name',
                'ColumnDescription' => 'name',
                'DataType'          => 'string',
                'RequestRule'       => 'required',
            ],
            [
                'ApiName'           => '',
                'ApiDescription'    => '',
                'GroupType'         => 'Response',
                'ColumnName'        => 'id',
                'ColumnDescription' => 'id',
                'DataType'          => 'int',
                'RequestRule'       => '',
            ],
            [
                'ApiName'           => '',
                'ApiDescription'    => '',
                'GroupType'         => '',
                'ColumnName'        => '',
                'ColumnDescription' => '',
                'DataType'          => '',
                'RequestRule'       => '',
            ],
            [
                'ApiName'           => 'Get2',
                'ApiDescription'    => 'get2',
                'GroupType'         => 'Request',
                'ColumnName'        => '',
                'ColumnDescription' => '',
                'DataType'          => '',
                'RequestRule'       => '',
            ],
            [
                'ApiName'           => '',
                'ApiDescription'    => '',
                'GroupType'         => 'Response',
                'ColumnName'        => 'id',
                'ColumnDescription' => 'id',
                'DataType'          => 'int',
                'RequestRule'       => '',
            ],
            [
                'ApiName'           => '',
                'ApiDescription'    => '',
                'GroupType'         => '',
                'ColumnName'        => 'name',
                'ColumnDescription' => 'name',
                'DataType'          => 'string',
                'RequestRule'       => '',
            ],
        ];

        $headerNamesChild = [
            'GroupType'         => 'GroupType',
            'ColumnName'        => 'ColumnName',
            'ColumnDescription' => 'ColumnDescription',
            'DataType'          => 'DataType',
            'RequestRule'       => 'RequestRule',
        ];

        // Group1
        $parentAttribute = new ParentAttribute('spreadSheetTitle', 'sheetName', $headerNamesChild);
        $parentAttribute->setParentAttributeDetails('Get', 'ApiName');
        $parentAttribute->setParentAttributeDetails('get', 'ApiDescription');
        $attribute = new Attribute();
        $attribute->setAttributeDetails('id', 'ColumnName');
        $attribute->setAttributeDetails('id', 'ColumnDescription');
        $attribute->setAttributeDetails('int', 'DataType');
        $attribute->setAttributeDetails('required|int', 'RequestRule');
        $attribute->setRuleMessage("{'required': 'required rule', 'int': 'int rule'}");
        $attribute2 = new Attribute();
        $attribute2->setAttributeDetails('name', 'ColumnName');
        $attribute2->setAttributeDetails('name', 'ColumnDescription');
        $attribute2->setAttributeDetails('string', 'DataType');
        $attribute2->setAttributeDetails('required', 'RequestRule');
        $attribute2->setRuleMessage("{'required': 'required rule'}");
        $parentAttribute->setAttributesGroup([$attribute, $attribute2], 'Request');
        $attribute3 = new Attribute();
        $attribute3->setAttributeDetails('id', 'ColumnName');
        $attribute3->setAttributeDetails('id', 'ColumnDescription');
        $attribute3->setAttributeDetails('int', 'DataType');
        $attribute3->setAttributeDetails('', 'RequestRule');
        $parentAttribute->setAttributesGroup([$attribute3], 'Response');

        // Group2
        $parentAttribute2 = new ParentAttribute('spreadSheetTitle', 'sheetName', $headerNamesChild);
        $parentAttribute2->setParentAttributeDetails('Get2', 'ApiName');
        $parentAttribute2->setParentAttributeDetails('get2', 'ApiDescription');
        $parentAttribute2->setAttributesGroup([], 'Request');
        $attribute4 = new Attribute();
        $attribute4->setAttributeDetails('id', 'ColumnName');
        $attribute4->setAttributeDetails('id', 'ColumnDescription');
        $attribute4->setAttributeDetails('int', 'DataType');
        $attribute4->setAttributeDetails('', 'RequestRule');
        $attribute5 = new Attribute();
        $attribute5->setAttributeDetails('name', 'ColumnName');
        $attribute5->setAttributeDetails('name', 'ColumnDescription');
        $attribute5->setAttributeDetails('string', 'DataType');
        $attribute5->setAttributeDetails('', 'RequestRule');
        $parentAttribute2->setAttributesGroup([$attribute4, $attribute5], 'Response');

        $readSpreadSheet = [
            'category_tag'                => 'TwoArea',
            'use_blade'                   => 'TwoAreaBlade',
            'sheet_id'                    => 'sheet_id',
            'output_directory_path'       => __DIR__.'/TestCode/output',
            'definition_directory_path'   => __DIR__.'/TestCode/definition',
            'separation_key'              => 'GroupType',
            'attribute_group_column_name' => 'GroupType',
            'file_extension'              => 'yml',
        ];

        $fileOperation = $this->app->make(FileOperation::class);
        $googleService = Mockery::mock(GoogleService::class);
        $googleService->allows('readFromGoogleServiceSheet')->andReturns($googleServiceSheet);
        $spreadSheetReader = new SpreadSheetReader($googleService);
        $bladeLoader = Mockery::mock(BladeLoader::class);
        $twoAreaCreator = new TwoAreaCreator($fileOperation, $spreadSheetReader, $bladeLoader, $readSpreadSheet);
        $response = $this->executePrivateFunction(
            $twoAreaCreator,
            'convertSheetData',
            [$sheetValues, 'spreadSheetTitle', 'sheetName']
        );
        self::assertEquals($response, [$parentAttribute, $parentAttribute2]);

        // rule message test.
        $ruleMessage1 = $response[0]->attributesGroup()['Request'][0]->ruleMessage();
        $ruleMessage2 = $response[0]->attributesGroup()['Request'][1]->ruleMessage();
        self::assertEquals("{'required': 'required rule', 'int': 'int rule'}", $ruleMessage1);
        self::assertEquals("{'required': 'required rule'}", $ruleMessage2);
    }

    /**
     * @test
     */
    public function multiGroup(): void
    {
        Config::set('stepupdream.spread-sheet-converter.request_rule_sheet_name', 'RequestRule');
        Config::set('stepupdream.spread-sheet-converter.request_rule_column_name', 'RequestRule');

        $googleServiceSheet = new GoogleServiceSheet('spreadSheetTitle', $this->mock);

        $sheetValues = [
            [
                'ApiName'           => 'Get',
                'ApiDescription'    => 'get',
                'GroupType'         => 'Group1',
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
                'GroupType'         => 'Group2',
                'ColumnName'        => 'id',
                'ColumnDescription' => 'id',
                'DataType'          => 'int',
            ],
            [
                'ApiName'           => '',
                'ApiDescription'    => '',
                'GroupType'         => 'Group3',
                'ColumnName'        => 'level',
                'ColumnDescription' => 'level',
                'DataType'          => 'int',
            ],
            [
                'ApiName'           => '',
                'ApiDescription'    => '',
                'GroupType'         => '',
                'ColumnName'        => '',
                'ColumnDescription' => '',
                'DataType'          => '',
            ],
            [
                'ApiName'           => 'Get2',
                'ApiDescription'    => 'get2',
                'GroupType'         => 'Group1',
                'ColumnName'        => '',
                'ColumnDescription' => '',
                'DataType'          => '',
            ],
            [
                'ApiName'           => '',
                'ApiDescription'    => '',
                'GroupType'         => 'Group2',
                'ColumnName'        => 'id',
                'ColumnDescription' => 'id',
                'DataType'          => 'int',
            ],
            [
                'ApiName'           => '',
                'ApiDescription'    => '',
                'GroupType'         => 'Group3',
                'ColumnName'        => 'level',
                'ColumnDescription' => 'level',
                'DataType'          => 'int',
            ],
        ];

        $headerNamesChild = [
            'GroupType'         => 'GroupType',
            'ColumnName'        => 'ColumnName',
            'ColumnDescription' => 'ColumnDescription',
            'DataType'          => 'DataType',
        ];

        // Group1
        $parentAttribute = new ParentAttribute('spreadSheetTitle', 'sheetName', $headerNamesChild);
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
        $parentAttribute->setAttributesGroup([$attribute, $attribute2], 'Group1');
        $attribute3 = new Attribute();
        $attribute3->setAttributeDetails('id', 'ColumnName');
        $attribute3->setAttributeDetails('id', 'ColumnDescription');
        $attribute3->setAttributeDetails('int', 'DataType');
        $parentAttribute->setAttributesGroup([$attribute3], 'Group2');
        $attribute4 = new Attribute();
        $attribute4->setAttributeDetails('level', 'ColumnName');
        $attribute4->setAttributeDetails('level', 'ColumnDescription');
        $attribute4->setAttributeDetails('int', 'DataType');
        $parentAttribute->setAttributesGroup([$attribute4], 'Group3');

        // Group2
        $parentAttribute2 = new ParentAttribute('spreadSheetTitle', 'sheetName', $headerNamesChild);
        $parentAttribute2->setParentAttributeDetails('Get2', 'ApiName');
        $parentAttribute2->setParentAttributeDetails('get2', 'ApiDescription');
        $parentAttribute2->setAttributesGroup([], 'Group1');
        $attribute4 = new Attribute();
        $attribute4->setAttributeDetails('id', 'ColumnName');
        $attribute4->setAttributeDetails('id', 'ColumnDescription');
        $attribute4->setAttributeDetails('int', 'DataType');
        $parentAttribute2->setAttributesGroup([$attribute4], 'Group2');
        $attribute5 = new Attribute();
        $attribute5->setAttributeDetails('level', 'ColumnName');
        $attribute5->setAttributeDetails('level', 'ColumnDescription');
        $attribute5->setAttributeDetails('int', 'DataType');
        $parentAttribute2->setAttributesGroup([$attribute5], 'Group3');

        $readSpreadSheet = [
            'category_tag'                => 'TwoArea',
            'use_blade'                   => 'TwoAreaBlade',
            'sheet_id'                    => 'sheet_id',
            'output_directory_path'       => __DIR__.'/TestCode/output',
            'definition_directory_path'   => __DIR__.'/TestCode/definition',
            'separation_key'              => 'GroupType',
            'attribute_group_column_name' => 'GroupType',
            'file_extension'              => 'yml',
        ];

        $fileOperation = $this->app->make(FileOperation::class);
        $googleService = Mockery::mock(GoogleService::class);
        $googleService->allows('readFromGoogleServiceSheet')->andReturns($googleServiceSheet);
        $spreadSheetReader = new SpreadSheetReader($googleService);
        $bladeLoader = Mockery::mock(BladeLoader::class);
        $twoAreaCreator = new TwoAreaCreator($fileOperation, $spreadSheetReader, $bladeLoader, $readSpreadSheet);
        $response = $this->executePrivateFunction(
            $twoAreaCreator,
            'convertSheetData',
            [$sheetValues, 'spreadSheetTitle', 'sheetName']
        );

        self::assertEquals($response, [$parentAttribute, $parentAttribute2]);
    }

    /**
     * @test
     */
    public function oneGroup(): void
    {
        Config::set('stepupdream.spread-sheet-converter.request_rule_sheet_name', 'RequestRule');
        Config::set('stepupdream.spread-sheet-converter.request_rule_column_name', 'RequestRule');

        $googleServiceSheet = new GoogleServiceSheet('spreadSheetTitle', $this->mock);

        $sheetValues = [
            [
                'ApiName'           => 'Get',
                'ApiDescription'    => 'get',
                'ColumnName'        => 'id',
                'ColumnDescription' => 'id',
                'DataType'          => 'int',
            ],
            [
                'ApiName'           => '',
                'ApiDescription'    => '',
                'ColumnName'        => 'name',
                'ColumnDescription' => 'name',
                'DataType'          => 'string',
            ],
            [
                'ApiName'           => '',
                'ApiDescription'    => '',
                'GroupType'         => '',
                'ColumnName'        => '',
                'ColumnDescription' => '',
                'DataType'          => '',
            ],
            [
                'ApiName'           => 'Get2',
                'ApiDescription'    => 'get2',
                'ColumnName'        => 'id',
                'ColumnDescription' => 'id',
                'DataType'          => 'int',
            ],
            [
                'ApiName'           => '',
                'ApiDescription'    => '',
                'ColumnName'        => 'level',
                'ColumnDescription' => 'level',
                'DataType'          => 'int',
            ],
        ];

        $headerNamesChild = [
            'ColumnName'        => 'ColumnName',
            'ColumnDescription' => 'ColumnDescription',
            'DataType'          => 'DataType',
        ];

        // Group1
        $parentAttribute = new ParentAttribute('spreadSheetTitle', 'sheetName', $headerNamesChild);
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
        $parentAttribute->setAttributesGroup([$attribute, $attribute2]);

        // Group2
        $parentAttribute2 = new ParentAttribute('spreadSheetTitle', 'sheetName', $headerNamesChild);
        $parentAttribute2->setParentAttributeDetails('Get2', 'ApiName');
        $parentAttribute2->setParentAttributeDetails('get2', 'ApiDescription');
        $attribute4 = new Attribute();
        $attribute4->setAttributeDetails('id', 'ColumnName');
        $attribute4->setAttributeDetails('id', 'ColumnDescription');
        $attribute4->setAttributeDetails('int', 'DataType');
        $attribute5 = new Attribute();
        $attribute5->setAttributeDetails('level', 'ColumnName');
        $attribute5->setAttributeDetails('level', 'ColumnDescription');
        $attribute5->setAttributeDetails('int', 'DataType');
        $parentAttribute2->setAttributesGroup([$attribute4, $attribute5]);

        $readSpreadSheet = [
            'category_tag'                => 'TwoArea',
            'use_blade'                   => 'TwoAreaBlade',
            'sheet_id'                    => 'sheet_id',
            'output_directory_path'       => __DIR__.'/TestCode/output',
            'definition_directory_path'   => __DIR__.'/TestCode/definition',
            'separation_key'              => 'ColumnName',
            'attribute_group_column_name' => null,
            'file_extension'              => 'yml',
        ];

        $fileOperation = $this->app->make(FileOperation::class);
        $googleService = Mockery::mock(GoogleService::class);
        $googleService->allows('readFromGoogleServiceSheet')->andReturns($googleServiceSheet);
        $spreadSheetReader = new SpreadSheetReader($googleService);
        $bladeLoader = Mockery::mock(BladeLoader::class);
        $twoAreaCreator = new TwoAreaCreator($fileOperation, $spreadSheetReader, $bladeLoader, $readSpreadSheet);
        $response = $this->executePrivateFunction(
            $twoAreaCreator,
            'convertSheetData',
            [$sheetValues, 'spreadSheetTitle', 'sheetName']
        );

        self::assertEquals($response, [$parentAttribute, $parentAttribute2]);
    }
}
