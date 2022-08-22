<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers;

use Google\Service\Sheets;
use Google\Service\Sheets\AppendValuesResponse;
use Google\Service\Sheets\BatchUpdateValuesResponse;
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
     * @param  array  $values
     * @param  string  $sheetName
     * @param  string  $range
     * @param  string  $option
     * @return \Google\Service\Sheets\BatchUpdateValuesResponse
     * @throws \Google\Exception
     */
    public function updateGoogleServiceSheet(
        string $sheetId,
        array $values,
        string $sheetName,
        string $range,
        string $option
    ): BatchUpdateValuesResponse {
        $spreadsheetService = $this->googleSpreadsheetService();
        $range = sprintf('%s!%s', $sheetName, $range);

        $valueRange = [];
        $valueRange[] = new Google_Service_Sheets_ValueRange([
            'range'  => $range,
            'values' => $values,
        ]);

        $body = new  Google_Service_Sheets_BatchUpdateValuesRequest([
            'valueInputOption' => $option,
            'data'             => $valueRange,
        ]);

        return $spreadsheetService->spreadsheets_values->batchUpdate($sheetId, $body);
    }

    /**
     * Append spreadsheet data.
     *
     * @param  string  $sheetId
     * @param  mixed[][]  $values
     * @param  string  $sheetName
     * @param  string  $range
     * @param  string  $option
     * @return \Google\Service\Sheets\AppendValuesResponse
     */
    public function appendGoogleServiceSheet(
        string $sheetId,
        array $values,
        string $sheetName,
        string $range,
        string $option
    ): AppendValuesResponse {
        $spreadsheetService = $this->googleSpreadsheetService();
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $values,
        ]);

        // USER_ENTERED or RAW
        // In case of RAW, values are displayed as they are.
        $params = ['valueInputOption' => $option];
        $range = sprintf('%s!%s', $sheetName, $range);

        return $spreadsheetService->spreadsheets_values->append($sheetId, $range, $body, $params);
    }

    /**
     * Object class for manipulating Spreadsheet.
     *
     * @return \Google\Service\Sheets
     * @throws \Google\Exception
     */
    protected function googleSpreadsheetService(): Sheets
    {
        $client = new Google_Client();
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig(__DIR__.'/credentials.json');

        return new Google_Service_Sheets($client);

        //        $credentialsPath = config('stepupdream.spread-sheet-converter.credentials_path');
//        return __DIR__.'/credentials.json';
    }
}
