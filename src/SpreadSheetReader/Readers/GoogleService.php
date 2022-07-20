<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers;

use Google_Client;
use Google_Service_Sheets;
use LogicException;

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
        $credentialsPath = $this->credentialsPath();

        $client = new Google_Client();
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig($credentialsPath);

        $googleServiceSheets = new Google_Service_Sheets($client);
        $spreadSheetsValues = [];

        $spreadSheets = $googleServiceSheets->spreadsheets->get($sheetId);
        $sheets = $spreadSheets->getSheets();
        foreach ($sheets as $sheet) {
            $targetSheet = $sheet->getProperties()->getTitle();
            $sheetDataRange = $googleServiceSheets->spreadsheets_values->get($sheetId, $targetSheet);
            $spreadSheetsValues[$targetSheet] = $sheetDataRange->getValues();
        }

        return new GoogleServiceSheet($spreadSheets->getProperties()->getTitle(), $spreadSheetsValues);
    }

    /**
     * Credentials path.
     *
     * @return string
     */
    protected function credentialsPath(): string
    {
        $credentialsPath = config('stepupdream.spread-sheet-converter.credentials_path');

        if (! is_string($credentialsPath) || $credentialsPath === '') {
            throw new LogicException('The name of the credentials path is incorrect.');
        }

        return $credentialsPath;
    }
}
