<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\SpreadSheetService\Readers;

use LogicException;
use StepUpDream\SpreadSheetConverter\SpreadSheetService\GoogleService;
use StepUpDream\SpreadSheetConverter\SpreadSheetService\GoogleServiceSheet;

class SpreadSheetReader
{
    /**
     * A cache of the first row in an array.
     *
     * @var string[][]
     */
    protected array $parentAttributeKeyName = [];

    /**
     * A cache of the first row in an array.
     *
     * @var string[][]
     */
    protected array $attributeKeyName = [];

    /**
     * @var GoogleServiceSheet[]
     *
     * key : sheet id.
     */
    protected array $googleServiceSheets = [];

    /**
     * @param  \StepUpDream\SpreadSheetConverter\SpreadSheetService\GoogleService  $googleService
     */
    public function __construct(protected GoogleService $googleService)
    {
    }

    /**
     * Read spreadsheet data.
     *
     * @param  string  $sheetId
     * @param  string  $targetSheetName
     * @return string[][] Table information array containing information for each sheet：key is sheet name.
     */
    public function readBySheetName(string $sheetId, string $targetSheetName): array
    {
        $spreadsheets = $this->read($sheetId);

        if (empty($spreadsheets[$targetSheetName])) {
            throw new LogicException('can not read sheet data: '.$targetSheetName);
        }

        return $spreadsheets[$targetSheetName];
    }

    /**
     * Read spreadsheet data.
     *
     * @param  string  $sheetId
     * @return string[][][] Table information array containing information for each sheet：key is sheet name.
     */
    public function read(string $sheetId): array
    {
        return $this->readSpreadSheetValue($sheetId);
    }

    /**
     * Read spreadsheet title.
     *
     * @param  string  $sheetId
     * @return string[][][] Table information array containing information for each sheet：key is sheet name.
     */
    protected function readSpreadSheetValue(string $sheetId): array
    {
        $googleServiceSheet = $this->readFromGoogleServiceSheet($sheetId);
        $spreadsheets = $googleServiceSheet->spreadSheets();
        foreach ($spreadsheets as $sheetName => $sheet) {
            $spreadsheets[$sheetName] = $this->getTitleArray($sheet, $sheetName);
        }

        return $spreadsheets;
    }

    /**
     * Read spreadsheet data.
     *
     * @param  string  $sheetId
     * @return GoogleServiceSheet Table information array containing information for each sheet：key is sheet name.
     */
    protected function readFromGoogleServiceSheet(string $sheetId): GoogleServiceSheet
    {
        if (! empty($this->googleServiceSheets[$sheetId])) {
            return $this->googleServiceSheets[$sheetId];
        }

        $readFromGoogleServiceSheet = $this->googleService->readFromGoogleServiceSheet($sheetId);

        $this->googleServiceSheets[$sheetId] = $readFromGoogleServiceSheet;

        return $this->googleServiceSheets[$sheetId];
    }

    /**
     * Make the first row the key of the associative array.
     *
     * @param  string[][]  $sheet
     * @param  string  $sheetName
     * @return string[][]
     */
    protected function getTitleArray(array $sheet, string $sheetName): array
    {
        $result = [];
        $headerRow = [];
        $isHeader = true;

        if (empty($sheet)) {
            throw new LogicException('need sheet header: '.$sheetName);
        }

        foreach ($sheet as $row) {
            if ($isHeader) {
                $headerRow = $row;
                $isHeader = false;
            } else {
                $rowWithKey = [];
                foreach ($headerRow as $key => $value) {
                    $rowWithKey[$value] = $row[$key] ?? '';
                }

                $result[] = $rowWithKey;
            }
        }

        return $result;
    }

    /**
     * Read spreadsheet data.
     *
     * @param  string  $sheetId
     * @return string
     */
    public function spreadSheetTitle(string $sheetId): string
    {
        return $this->readFromGoogleServiceSheet($sheetId)->spreadSheetTitle();
    }

    /**
     * Verification of correct type specification.
     *
     * @param  string[]  $attribute
     * @noinspection PhpUnused
     */
    public function verifySheetDataDetail(array $attribute): void
    {
        // Optional
    }

    /**
     * Gets the first row of the array up to the specified key.
     *
     * @param  string[][]  $sheet
     * @param  string  $separationKey
     * @return string[] Sheet header list
     */
    public function getParentAttributeKeyName(array $sheet, string $separationKey): array
    {
        $sheetFirstRow = collect($sheet)->first();
        $cacheKey = (string) collect($sheetFirstRow)->first();
        $names = [];

        if (! empty($this->parentAttributeKeyName[$cacheKey])) {
            return $this->parentAttributeKeyName[$cacheKey];
        }

        if ($sheetFirstRow === null) {
            throw new LogicException('The value of sheet first row is not an array');
        }

        foreach ($sheetFirstRow as $key => $value) {
            if ($key === $separationKey) {
                break;
            }
            $names[$key] = $key;
        }

        $this->parentAttributeKeyName[$cacheKey] = $names;

        return $names;
    }

    /**
     * Gets the first row of the array after the specified key.
     *
     * @param  string[][]  $sheet
     * @param  string  $separationKey
     * @return string[] Sheet header list
     */
    public function getAttributeKeyName(array $sheet, string $separationKey): array
    {
        $sheetFirstRow = collect($sheet)->first();
        $shouldAddStart = false;
        $names = [];

        $cacheKey = (string) collect($sheetFirstRow)->first();

        if (! empty($this->attributeKeyName[$cacheKey])) {
            return $this->attributeKeyName[$cacheKey];
        }

        if ($sheetFirstRow === null) {
            throw new LogicException('The value of sheet first row is not an array');
        }

        // Get what's to the right of the separation key part of the header row in Spreadsheet.
        foreach ($sheetFirstRow as $key => $value) {
            if ($key === $separationKey) {
                $shouldAddStart = true;
            }

            if ($shouldAddStart) {
                $names[$key] = $key;
            }
        }

        $this->attributeKeyName[$cacheKey] = $names;

        return $names;
    }

    /**
     * Whether the entire row is all empty.
     *
     * @param  string[]  $values
     * @return bool
     */
    public function isAllEmpty(array $values): bool
    {
        foreach ($values as $value) {
            if ($value !== '') {
                return false;
            }
        }

        return true;
    }
}
