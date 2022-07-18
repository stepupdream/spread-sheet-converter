<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers;

use Google_Client;
use Google_Service_Sheets;

class GoogleService
{
    /**
     * Read spreadsheet data.
     *
     * @param  string  $sheetId
     * @return string[][][] Table information array containing information for each sheet：key is sheet name.
     */
    public function readFromGoogleServiceSheet(string $sheetId): array
    {
        $credentialsPath = config('step_up_dream.spread_sheet_converter.credentials_path');

        $client = new Google_Client();
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig($credentialsPath);

        $googleServiceSheets = new Google_Service_Sheets($client);
        $spreadsheets = [];

        $sheets = $googleServiceSheets->spreadsheets->get($sheetId)->getSheets();
        foreach ($sheets as $sheet) {
            $targetSheet = $sheet->getProperties()->getTitle();
            $sheetDataRange = $googleServiceSheets->spreadsheets_values->get($sheetId, $targetSheet);
            $spreadsheets[$targetSheet] = $sheetDataRange->getValues();
        }

        return $spreadsheets;
    }
}