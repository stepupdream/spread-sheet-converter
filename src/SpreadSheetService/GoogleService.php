<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\SpreadSheetService;

use Exception;
use Google\Service\Sheets;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_BatchUpdateValuesRequest;
use Google_Service_Sheets_ValueRange;

class GoogleService
{
    /**
     * Read spreadsheet data.
     *
     * @param  string  $sheetId
     * @return GoogleServiceSheet Table information array containing information for each sheet：key is sheet name.
     */
    public function readFromGoogleServiceSheet(string $sheetId): GoogleServiceSheet
    {
        $spreadsheetService = $this->googleSpreadsheetService();
        $spreadSheetsValues = [];

        $spreadSheets = $spreadsheetService->spreadsheets->get($sheetId);
        $sheets = $spreadSheets->getSheets();
        foreach ($sheets as $sheet) {
            $targetSheet = $sheet->getProperties()->getTitle();
            $sheetDataRange = $spreadsheetService->spreadsheets_values->get($sheetId, $targetSheet);
            $spreadSheetsValues[$targetSheet] = $sheetDataRange->getValues();
        }

        return new GoogleServiceSheet($spreadSheets->getProperties()->getTitle(), $spreadSheetsValues);
    }

    /**
     * Update spreadsheet data.
     *
     * @param  string  $sheetId
     * @param  mixed[][]  $values
     * @param  string  $sheetName
     * @param  string  $range
     * @param  string  $option
     * @return string
     * @throws \Google\Exception
     */
    public function updateGoogleServiceSheet(
        string $sheetId,
        array $values,
        string $sheetName,
        string $range,
        string $option
    ): string {
        $spreadsheetService = $this->googleSpreadsheetService();
        $range = sprintf('%s!%s', $sheetName, $range);

        $valueRange = [];
        $valueRange[] = new Google_Service_Sheets_ValueRange([
            'range' => $range, 'values' => $values,
        ]);

        $body = new  Google_Service_Sheets_BatchUpdateValuesRequest([
            'valueInputOption' => $option, 'data' => $valueRange,
        ]);

        try {
            $spreadsheetService->spreadsheets_values->batchUpdate($sheetId, $body);

            return 'DONE';
        } catch (Exception $e) {
            throw new \Google\Exception($e->getMessage());
        }
    }

    /**
     * Append spreadsheet data.
     *
     * @param  string  $sheetId
     * @param  mixed[][]  $values
     * @param  string  $sheetName
     * @param  string  $range
     * @param  string  $option
     * @return string
     * @throws \Google\Exception
     */
    public function appendGoogleServiceSheet(
        string $sheetId,
        array $values,
        string $sheetName,
        string $range,
        string $option
    ): string {
        $spreadsheetService = $this->googleSpreadsheetService();
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $values,
        ]);

        // USER_ENTERED or RAW
        // In case of RAW, values are displayed as they are.
        $params = ['valueInputOption' => $option];
        $range = sprintf('%s!%s', $sheetName, $range);

        try {
            $spreadsheetService->spreadsheets_values->append($sheetId, $range, $body, $params);

            return 'DONE';
        } catch (Exception $e) {
            throw new \Google\Exception($e->getMessage());
        }
    }

    /**
     * Object class for manipulating Spreadsheet.
     *
     * @return \Google\Service\Sheets
     * @throws \Google\Exception
     */
    protected function googleSpreadsheetService(): Sheets
    {
        $credentialsPath = config('stepupdream.spread-sheet-converter.credentials_path');

        $client = new Google_Client();
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig($credentialsPath);

        return new Google_Service_Sheets($client);
    }
}
