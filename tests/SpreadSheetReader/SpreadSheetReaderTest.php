<?php

declare(strict_types=1);

use StepUpDream\SpreadSheetConverter\SpreadSheetService\GoogleService;
use StepUpDream\SpreadSheetConverter\SpreadSheetService\GoogleServiceSheet;
use StepUpDream\SpreadSheetConverter\SpreadSheetService\Readers\SpreadSheetReader;

beforeEach(function () {
    $this->googleService = mock(GoogleService::class);
    $this->reader = new SpreadSheetReader($this->googleService);
});

describe('readBySheetName', function () {
    test('returns correct sheet data when a sheet exists', function () {
        $sheetId = '123';
        $sheetName = 'TestSheet';
        $expectedData = [
            ['header1' => 'value1', 'header2' => 'value2'],
        ];

        $googleServiceSheet = mock(GoogleServiceSheet::class);
        $this->googleService->shouldReceive('readFromGoogleServiceSheet')
            ->once()
            ->with($sheetId)
            ->andReturn($googleServiceSheet);

        $googleServiceSheet->shouldReceive('spreadSheets')
            ->once()
            ->andReturn([
                $sheetName => [
                    ['header1', 'header2'],
                    ['value1', 'value2'],
                ],
            ]);

        expect($this->reader->readBySheetName($sheetId, $sheetName))
            ->toBe($expectedData);
    });

    test('throws exception when a sheet does not exist', function () {
        $sheetId = '123';
        $nonExistentSheet = 'NonExistentSheet';

        $googleServiceSheet = mock(GoogleServiceSheet::class);
        $this->googleService->shouldReceive('readFromGoogleServiceSheet')
            ->once()
            ->with($sheetId)
            ->andReturn($googleServiceSheet);

        $googleServiceSheet->shouldReceive('spreadSheets')
            ->once()
            ->andReturn([]);

        expect(fn () => $this->reader->readBySheetName($sheetId, $nonExistentSheet))
            ->toThrow(LogicException::class, "can't read sheet data: $nonExistentSheet");
    });
});

describe('read', function () {
    test('returns processed sheet data for all sheets', function () {
        $sheetId = '123';
        $sheets = [
            'Sheet1' => [
                ['header1', 'header2'],
                ['value1', 'value2'],
            ],
            'Sheet2' => [
                ['header3', 'header4'],
                ['value3', 'value4'],
            ],
        ];

        $expectedData = [
            'Sheet1' => [
                ['header1' => 'value1', 'header2' => 'value2'],
            ],
            'Sheet2' => [
                ['header3' => 'value3', 'header4' => 'value4'],
            ],
        ];

        $googleServiceSheet = mock(GoogleServiceSheet::class);
        $this->googleService->shouldReceive('readFromGoogleServiceSheet')
            ->once()
            ->with($sheetId)
            ->andReturn($googleServiceSheet);

        $googleServiceSheet->shouldReceive('spreadSheets')
            ->once()
            ->andReturn($sheets);

        expect($this->reader->read($sheetId))
            ->toBe($expectedData);
    });
});

describe('spreadSheetTitle', function () {
    test('returns correct spreadsheet title', function () {
        $sheetId = '123';
        $expectedTitle = 'Test Spreadsheet';

        $googleServiceSheet = mock(GoogleServiceSheet::class);
        $this->googleService->shouldReceive('readFromGoogleServiceSheet')
            ->once()
            ->with($sheetId)
            ->andReturn($googleServiceSheet);

        $googleServiceSheet->shouldReceive('spreadSheetTitle')
            ->once()
            ->andReturn($expectedTitle);

        expect($this->reader->spreadSheetTitle($sheetId))
            ->toBe($expectedTitle);
    });
});

describe('getParentAttributeKeyName', function () {
    test('returns keys up to a separation key', function () {
        $sheet = [
            ['key1' => 'val1', 'separator' => 'sep', 'key3' => 'val3'],
        ];
        $separationKey = 'separator';
        $expected = ['key1' => 'key1'];

        expect($this->reader->getParentAttributeKeyName($sheet, $separationKey))
            ->toBe($expected);
    });

    test('throws exception when a sheet is empty', function () {
        $sheet = [];
        $separationKey = 'separator';

        expect(fn () => $this->reader->getParentAttributeKeyName($sheet, $separationKey))
            ->toThrow(LogicException::class, 'The value of sheet-first row is not an array');
    });
});

describe('getAttributeKeyName', function () {
    test('returns keys after a separation key', function () {
        $sheet = [
            ['key1' => 'val1', 'separator' => 'sep', 'key3' => 'val3'],
        ];
        $separationKey = 'separator';
        $expected = ['separator' => 'separator', 'key3' => 'key3'];

        expect($this->reader->getAttributeKeyName($sheet, $separationKey))
            ->toBe($expected);
    });

    test('throws exception when a sheet is empty', function () {
        $sheet = [];
        $separationKey = 'separator';

        expect(fn () => $this->reader->getAttributeKeyName($sheet, $separationKey))
            ->toThrow(LogicException::class, 'The value of sheet-first row is not an array');
    });
});

describe('isAllEmpty', function () {
    test('returns true when all values are empty', function () {
        $values = ['', '', ''];

        expect($this->reader->isRowEmpty($values))
            ->toBeTrue();
    });

    test('returns are false when any value is not empty', function () {
        $values = ['', 'not empty', ''];

        expect($this->reader->isRowEmpty($values))
            ->toBeFalse();
    });
});
