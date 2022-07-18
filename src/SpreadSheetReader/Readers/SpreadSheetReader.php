<?php

namespace StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers;

use Google_Client;
use Google_Service_Sheets;
use LogicException;

/**
 * Class SpreadSheetReader.
 */
class SpreadSheetReader
{
    /**
     * A cache of the first row in an array.
     *
     * @var array
     */
    protected $parentAttributeKeyName = [];

    /**
     * A cache of the first row in an array.
     *
     * @var array
     */
    protected $attributeKeyName = [];

    /**
     * Read spreadsheet data.
     *
     * @param  string  $sheetId
     * @param  string|null  $targetSheetName
     * @return array Table information array containing information for each sheet：key is sheet name
     */
    public function read(string $sheetId, string $targetSheetName = null): array
    {
        $spreadsheets = $this->readFromGoogleServiceSheet($sheetId);

        if (isset($targetSheetName)) {
            if (empty($spreadsheets[$targetSheetName])) {
                throw new LogicException('can not read sheet data: '.$targetSheetName);
            }

            return $spreadsheets[$targetSheetName];
        }

        return $spreadsheets;
    }

    /**
     * Verification of correct type specification.
     *
     * @param  array  $attribute
     */
    public function verifySheetDataDetail(array $attribute): void
    {
        // Optional
    }

    /**
     * Gets the first row of the array up to the specified key.
     *
     * @param  array  $sheet
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
     * @param  array  $sheet
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

        // Get what's to the right of the separation key part of the header row in Spreadsheet
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
     * @param  array  $values
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

    /**
     * Read spreadsheet data.
     *
     * @param  string  $sheetId
     * @return array Table information array containing information for each sheet：key is sheet name
     */
    protected function readFromGoogleServiceSheet(string $sheetId): array
    {
        $credentialsPath = config('step_up_dream.spread_sheet_converter.credentials_path');

        $client = new Google_Client();
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig($credentialsPath);

        $sheets = new Google_Service_Sheets($client);
        $spreadsheets = [];

        foreach ($sheets->spreadsheets->get($sheetId)->getSheets() as $sheet) {
            $targetSheet = $sheet->getProperties()->getTitle();
            $sheetDataRange = $sheets->spreadsheets_values->get($sheetId, $targetSheet);
            $spreadsheets[$targetSheet] = $this->getTitleArray($sheetDataRange->getValues(), $targetSheet);
        }

        return $spreadsheets;
    }

    /**
     * Make the first row the key of the associative array.
     *
     * @param  array  $sheetValues
     * @param  string  $targetSheet
     * @return array
     */
    protected function getTitleArray(array $sheetValues, string $targetSheet): array
    {
        $result = [];
        $headerRow = [];
        $isHeader = true;

        if (empty($sheetValues)) {
            throw new LogicException('need sheet header: '.$targetSheet);
        }

        foreach ($sheetValues as $row) {
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
}
