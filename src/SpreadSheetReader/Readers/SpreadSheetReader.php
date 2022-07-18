<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers;

use LogicException;

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
     * @param  \StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers\GoogleService  $googleService
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
        return $this->readFromGoogleServiceSheet($sheetId);
    }

    /**
     * Read spreadsheet data.
     *
     * @param  string  $sheetId
     * @return string[][][] Table information array containing information for each sheet：key is sheet name.
     */
    protected function readFromGoogleServiceSheet(string $sheetId): array
    {
        $spreadsheets = $this->googleService->readFromGoogleServiceSheet($sheetId);
        foreach ($spreadsheets as $sheetTitle => $sheet) {
            $spreadsheets[$sheetTitle] = $this->getTitleArray($sheet, $sheetTitle);
        }

        return $spreadsheets;
    }

    /**
     * Make the first row the key of the associative array.
     *
     * @param  string[][]  $sheet
     * @param  string  $sheetTitle
     * @return string[][]
     */
    protected function getTitleArray(array $sheet, string $sheetTitle): array
    {
        $result = [];
        $headerRow = [];
        $isHeader = true;

        if (empty($sheet)) {
            throw new LogicException('need sheet header: '.$sheetTitle);
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
     * Verification of correct type specification.
     *
     * @param  string[]  $attribute
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
        $cacheKey = collect($sheetFirstRow)->first();
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

        $cacheKey = collect($sheetFirstRow)->first();

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
