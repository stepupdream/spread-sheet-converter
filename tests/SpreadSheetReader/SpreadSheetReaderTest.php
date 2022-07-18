<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\Test\SpreadSheetReader;

use Mockery;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers\GoogleService;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers\SpreadSheetReader;
use StepUpDream\SpreadSheetConverter\Test\TestCase;

class SpreadSheetReaderTest extends TestCase
{
    protected array $sheetValues = [
        'sheet_title1' => [
            [
                'TableName',
                'TableDescription',
                'ColumnName',
                'ColumnDescription',
            ],
            [
                'characters',
                'CharacterData',
                'id',
                'id',
            ],
            [
                '',
                '',
                'name',
                'name',
            ],
        ],
        'sheet_title2' => [
            [
                'TableName',
                'TableDescription',
                'ColumnName',
                'ColumnDescription',
            ],
            [
                'characters2',
                'CharacterData2',
                'id',
                'id',
            ],
            [
                '',
                '',
                'name2',
                'name2',
            ],
        ],
    ];

    /**
     * @test
     */
    public function read(): void
    {
        $resultValues = [
            'sheet_title1' => [
                [
                    'TableName'         => 'characters',
                    'TableDescription'  => 'CharacterData',
                    'ColumnName'        => 'id',
                    'ColumnDescription' => 'id',
                ],
                [
                    'TableName'         => '',
                    'TableDescription'  => '',
                    'ColumnName'        => 'name',
                    'ColumnDescription' => 'name',
                ],
            ],
            'sheet_title2' => [
                [
                    'TableName'         => 'characters2',
                    'TableDescription'  => 'CharacterData2',
                    'ColumnName'        => 'id',
                    'ColumnDescription' => 'id',
                ],
                [
                    'TableName'         => '',
                    'TableDescription'  => '',
                    'ColumnName'        => 'name2',
                    'ColumnDescription' => 'name2',
                ],
            ],
        ];

        $mock = Mockery::mock(GoogleService::class);
        $mock->allows('readFromGoogleServiceSheet')->andReturns($this->sheetValues);
        $spreadSheetReaderMock = new SpreadSheetReader($mock);

        $response = $spreadSheetReaderMock->read('sheet_id');
        self::assertEquals($response, $resultValues);

        $response = $spreadSheetReaderMock->readBySheetName('sheet_id', 'sheet_title1');
        self::assertEquals($response, $resultValues['sheet_title1']);
    }

    /**
     * @test
     */
    public function isAllEmpty(): void
    {
        $values = ['TableName' => '', 'TableDescription' => '', 'ColumnName' => '', 'ColumnDescription' => ''];
        $values2 = ['TableName' => 'hoge', 'TableDescription' => '', 'ColumnName' => '', 'ColumnDescription' => ''];

        $mock = Mockery::mock(GoogleService::class);
        $mock->allows('readFromGoogleServiceSheet')->andReturns($this->sheetValues);
        $spreadSheetReaderMock = new SpreadSheetReader($mock);
        $isAllEmpty = $spreadSheetReaderMock->isAllEmpty($values);
        self::assertTrue($isAllEmpty);

        $isAllEmpty2 = $spreadSheetReaderMock->isAllEmpty($values2);
        self::assertFalse($isAllEmpty2);
    }

    /**
     * @test
     */
    public function getAttributeKeyName(): void
    {
        $sheet = [
            [
                'TableName'         => 'characters',
                'TableDescription'  => 'CharacterData',
                'ColumnName'        => 'id',
                'ColumnDescription' => 'id',
            ],
            [
                'TableName'         => '',
                'TableDescription'  => '',
                'ColumnName'        => 'name',
                'ColumnDescription' => 'name',
            ],
        ];

        $mock = Mockery::mock(GoogleService::class);
        $mock->allows('readFromGoogleServiceSheet')->andReturns($this->sheetValues);
        $spreadSheetReaderMock = new SpreadSheetReader($mock);
        $attributeKeyName = $spreadSheetReaderMock->getAttributeKeyName($sheet, 'ColumnName');
        $testResult = ['ColumnName' => 'ColumnName', 'ColumnDescription' => 'ColumnDescription'];

        self::assertEquals($attributeKeyName, $testResult);
    }

    /**
     * @test
     */
    public function getParentAttributeKeyName(): void
    {
        $sheet = [
            [
                'TableName'         => 'characters',
                'TableDescription'  => 'CharacterData',
                'ColumnName'        => 'id',
                'ColumnDescription' => 'id',
            ],
            [
                'TableName'         => '',
                'TableDescription'  => '',
                'ColumnName'        => 'name',
                'ColumnDescription' => 'name',
            ],
        ];

        $mock = Mockery::mock(GoogleService::class);
        $mock->allows('readFromGoogleServiceSheet')->andReturns($this->sheetValues);
        $spreadSheetReaderMock = new SpreadSheetReader($mock);
        $parentAttributeKeyName = $spreadSheetReaderMock->getParentAttributeKeyName($sheet, 'ColumnName');
        $testResult = ['TableName' => 'TableName', 'TableDescription' => 'TableDescription'];

        self::assertEquals($parentAttributeKeyName, $testResult);
    }
}
