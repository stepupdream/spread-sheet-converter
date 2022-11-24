<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\Test\DefinitionDocument\Creators;

use Illuminate\Console\OutputStyle;
use Illuminate\Filesystem\Filesystem;
use Mockery;
use StepUpDream\DreamAbilitySupport\Supports\File\FileOperation;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\BladeLoader;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\OneAreaCreator;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute;
use StepUpDream\SpreadSheetConverter\SpreadSheetService\GoogleService;
use StepUpDream\SpreadSheetConverter\SpreadSheetService\GoogleServiceSheet;
use StepUpDream\SpreadSheetConverter\SpreadSheetService\Readers\SpreadSheetReader;
use StepUpDream\SpreadSheetConverter\Test\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class OneAreaCreatorTest extends TestCase
{
    /**
     * @test
     */
    public function oneAreaCreator(): void
    {
        $googleServiceSheet = new GoogleServiceSheet('spreadSheetTitle', [
            'sheetName1' => [
                [
                    'TableName',
                    'TableDescription',
                    'ColumnName',
                    'ColumnDescription',
                    'DataType',
                ],
                [
                    'characters1',
                    'CharacterData1',
                    'name1',
                    'name1',
                    'string1',
                ],
                [
                    'characters2',
                    'CharacterData2',
                    'name2',
                    'name2',
                    'string2',
                ],
            ],
        ]);

        $sheetValues = [
            'sheetName1' => [
                [
                    'TableName'         => 'characters1',
                    'TableDescription'  => 'CharacterData1',
                    'ColumnName'        => 'name1',
                    'ColumnDescription' => 'name1',
                    'DataType'          => 'string1',
                ],
                [
                    'TableName'         => 'characters2',
                    'TableDescription'  => 'CharacterData2',
                    'ColumnName'        => 'name2',
                    'ColumnDescription' => 'name2',
                    'DataType'          => 'string2',
                ],
            ],
        ];

        $readSpreadSheet = [
            'category_tag'              => 'OneArea',
            'use_blade'                 => 'other_data',
            'sheet_id'                  => 'sheet_id',
            'output_directory_path'     => __DIR__.'/TestCode/output',
            'definition_directory_path' => __DIR__.'/TestCode/definition',
            'file_extension'            => 'yml',
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

        $bufferedOutput = new BufferedOutput();
        $style = new OutputStyle(new ArrayInput([]), $bufferedOutput);
        $oneAreaCreator = new OneAreaCreator($fileOperation, $spreadSheetReader, $bladeLoader, $readSpreadSheet);
        $oneAreaCreator->setOutput($style)->run(null);

        $readSpreadSheetValue = $spreadSheetReader->read('sheet_id');
        self::assertEquals($readSpreadSheetValue, $sheetValues);

        $fileContent = file_get_contents(__DIR__.'/TestCode/output/sheetName1.yml');
        self::assertEquals('test', $fileContent);
        $filesystem->deleteDirectory(__DIR__.'/TestCode');
    }

    /**
     * @test
     */
    public function convertSheetData(): void
    {
        $sheetValues = [
            [
                'TableName'         => 'characters1',
                'TableDescription'  => 'CharacterData1',
                'ColumnName'        => 'name1',
                'ColumnDescription' => 'name1',
                'DataType'          => 'string1',
            ],
            [
                'TableName'         => 'characters2',
                'TableDescription'  => 'CharacterData2',
                'ColumnName'        => 'name2',
                'ColumnDescription' => 'name2',
                'DataType'          => 'string2',
            ],
        ];

        $headerNamesChild = [
            'TableName'         => 'TableName',
            'TableDescription'  => 'TableDescription',
            'ColumnName'        => 'ColumnName',
            'ColumnDescription' => 'ColumnDescription',
            'DataType'          => 'DataType',
        ];

        // Group1
        $parentAttribute = new ParentAttribute('spreadSheetTitle', 'sheetName', $headerNamesChild);
        $attribute = new Attribute();
        $attribute->setAttributeDetails('characters1', 'TableName');
        $attribute->setAttributeDetails('CharacterData1', 'TableDescription');
        $attribute->setAttributeDetails('name1', 'ColumnName');
        $attribute->setAttributeDetails('name1', 'ColumnDescription');
        $attribute->setAttributeDetails('string1', 'DataType');
        $attribute2 = new Attribute();
        $attribute2->setAttributeDetails('characters2', 'TableName');
        $attribute2->setAttributeDetails('CharacterData2', 'TableDescription');
        $attribute2->setAttributeDetails('name2', 'ColumnName');
        $attribute2->setAttributeDetails('name2', 'ColumnDescription');
        $attribute2->setAttributeDetails('string2', 'DataType');
        $parentAttribute->setAttributesGroup([$attribute, $attribute2]);

        $readSpreadSheet = [
            'category_tag'              => 'OneArea',
            'use_blade'                 => 'other_data',
            'sheet_id'                  => 'sheet_id',
            'output_directory_path'     => __DIR__.'/TestCode/output',
            'definition_directory_path' => __DIR__.'/TestCode/definition',
            'file_extension'            => 'yml',
        ];
        $fileOperation = $this->app->make(FileOperation::class);
        $googleService = Mockery::mock(GoogleService::class);
        $spreadSheetReader = new SpreadSheetReader($googleService);
        $bladeLoader = Mockery::mock(BladeLoader::class);

        $oneAreaCreator = new OneAreaCreator($fileOperation, $spreadSheetReader, $bladeLoader, $readSpreadSheet);
        $response = $this->executePrivateFunction(
            $oneAreaCreator,
            'convertSheetData',
            [$sheetValues, 'spreadSheetTitle', 'sheetName']
        );

        self::assertEquals($response, [$parentAttribute]);
    }
}
