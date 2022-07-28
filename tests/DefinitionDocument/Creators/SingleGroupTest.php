<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\Test\DefinitionDocument\Creators;

use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\SingleGroup;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers\SpreadSheetReader;
use StepUpDream\SpreadSheetConverter\Test\TestCase;

class SingleGroupTest extends TestCase
{
    /**
     * @test
     */
    public function convertSheetData(): void
    {
        $sheetValues = [
            [
                'TableName'         => 'characters',
                'TableDescription'  => 'CharacterData',
                'ColumnName'        => 'id',
                'ColumnDescription' => 'id',
                'DataType'          => 'int',
            ],
            [
                'TableName'         => '',
                'TableDescription'  => '',
                'ColumnName'        => 'name',
                'ColumnDescription' => 'name',
                'DataType'          => 'string',
            ],
            [
                'TableName'         => '',
                'TableDescription'  => '',
                'ColumnName'        => '',
                'ColumnDescription' => '',
                'DataType'          => '',
            ],
            [
                'TableName'         => 'equipments',
                'TableDescription'  => 'EquipmentData',
                'ColumnName'        => 'id',
                'ColumnDescription' => 'id',
                'DataType'          => 'int',
            ],
        ];

        $headerNamesChild = [
            'ColumnName'        => 'ColumnName',
            'ColumnDescription' => 'ColumnDescription',
            'DataType'          => 'DataType',
        ];

        // Group1
        $parentAttribute = new ParentAttribute('MasterData', 'sheetName', $headerNamesChild);
        $parentAttribute->setParentAttributeDetails('characters', 'TableName');
        $parentAttribute->setParentAttributeDetails('CharacterData', 'TableDescription');
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
        $parentAttribute2 = new ParentAttribute('MasterData', 'sheetName', $headerNamesChild);
        $parentAttribute2->setParentAttributeDetails('equipments', 'TableName');
        $parentAttribute2->setParentAttributeDetails('EquipmentData', 'TableDescription');
        $attribute = new Attribute();
        $attribute->setAttributeDetails('id', 'ColumnName');
        $attribute->setAttributeDetails('id', 'ColumnDescription');
        $attribute->setAttributeDetails('int', 'DataType');
        $parentAttribute2->setAttributesGroup([$attribute]);

        $argument = [
            'category_tag'                => 'MasterData',
            'use_blade'                   => 'master_data',
            'sheet_id'                    => 'sheet_id',
            'output_directory_path'       => base_path('definition_document/tmp/database/master_data'),
            'definition_directory_path'   => base_path('definition_document/database/master_data'),
            'separation_key'              => 'ColumnName',
            'attribute_group_column_name' => null,
        ];
        $fileOperation = $this->app->make(FileOperation::class);
        $spreadSheetReader = $this->app->make(SpreadSheetReader::class);

        $singleGroup = new SingleGroup($fileOperation, $spreadSheetReader, $argument);
        $response = $singleGroup->convertSheetData($sheetValues, 'MasterData', 'sheetName');

        self::assertEquals($response, [$parentAttribute, $parentAttribute2]);
    }
}
