<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\Test\DefinitionDocument\Creators;

use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\Other;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers\SpreadSheetReader;
use StepUpDream\SpreadSheetConverter\Test\TestCase;

class OtherTest extends TestCase
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
                'TableName'         => 'characters2',
                'TableDescription'  => 'CharacterData2',
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
            'TableName'         => 'TableName',
            'TableDescription'  => 'TableDescription',
            'ColumnName'        => 'ColumnName',
            'ColumnDescription' => 'ColumnDescription',
            'DataType'          => 'DataType',
        ];

        // Group1
        $parentAttribute = new ParentAttribute('Other', 'sheetName', $headerNamesChild);
        $attribute = new Attribute();
        $attribute->setAttributeDetails('characters', 'TableName');
        $attribute->setAttributeDetails('CharacterData', 'TableDescription');
        $attribute->setAttributeDetails('id', 'ColumnName');
        $attribute->setAttributeDetails('id', 'ColumnDescription');
        $attribute->setAttributeDetails('int', 'DataType');
        $attribute2 = new Attribute();
        $attribute2->setAttributeDetails('characters2', 'TableName');
        $attribute2->setAttributeDetails('CharacterData2', 'TableDescription');
        $attribute2->setAttributeDetails('name', 'ColumnName');
        $attribute2->setAttributeDetails('name', 'ColumnDescription');
        $attribute2->setAttributeDetails('string', 'DataType');
        $parentAttribute->setAttributesGroup([$attribute, $attribute2]);

        // Group2
        $parentAttribute2 = new ParentAttribute('Other', 'sheetName', $headerNamesChild);
        $attribute = new Attribute();
        $attribute->setAttributeDetails('equipments', 'TableName');
        $attribute->setAttributeDetails('EquipmentData', 'TableDescription');
        $attribute->setAttributeDetails('id', 'ColumnName');
        $attribute->setAttributeDetails('id', 'ColumnDescription');
        $attribute->setAttributeDetails('int', 'DataType');
        $parentAttribute2->setAttributesGroup([$attribute]);

        $argument = [
            'category_tag'                => 'Other',
            'use_blade'                   => 'other_data',
            'sheet_id'                    => 'sheet_id',
            'output_directory_path'       => base_path('definition_document/database/master_data'),
            'separation_key'              => 'ColumnName',
            'attribute_group_column_name' => null,
        ];
        $fileOperation = $this->app->make(FileOperation::class);
        $spreadSheetReader = $this->app->make(SpreadSheetReader::class);

        $other = new Other($fileOperation, $spreadSheetReader, $argument);
        $response = $other->convertSheetData($sheetValues, 'Other', 'sheetName');

        self::assertEquals($response, [$parentAttribute, $parentAttribute2]);
    }
}
