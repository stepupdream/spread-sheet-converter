<?php

namespace StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers;

use Google_Client;
use Google_Service_Sheets;
use LogicException;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports\SheetOperation;

/**
 * Class BaseReader
 *
 * @package StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers
 */
class BaseReader
{
    /**
     * @var \StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports\SheetOperation
     */
    protected $sheetOperation;
    
    /**
     * BaseReader constructor.
     *
     * @param  \StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports\SheetOperation  $sheetOperation
     */
    public function __construct(
        SheetOperation $sheetOperation
    ) {
        $this->sheetOperation = $sheetOperation;
    }
    
    /**
     * Read spreadsheet data
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
     * Read spreadsheet data
     *
     * @param  string  $sheetId
     * @return array Table information array containing information for each sheet：key is sheet name
     */
    public function readFromGoogleServiceSheet(string $sheetId): array
    {
        $credentialsPath = config('spread_sheet.credentials_path');
        
        $client = new Google_Client();
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig($credentialsPath);
        
        $sheets = new Google_Service_Sheets($client);
        $spreadsheets = [];
        
        foreach ($sheets->spreadsheets->get($sheetId)->getSheets() as $sheet) {
            $targetSheet = $sheet->getProperties()->getTitle();
            $sheetDataRange = $sheets->spreadsheets_values->get($sheetId, $targetSheet);
            $spreadsheets[$targetSheet] = $this->sheetOperation->getTitleArray($sheetDataRange->getValues(), $targetSheet);
        }
        
        return $spreadsheets;
    }
    
    /**
     * Verification of correct type specification
     *
     * @param  string  $name
     * @param  string  $dataType
     */
    public function verifyDataTypeDetail(string $name, string $dataType): void
    {
        // Optional
    }
}
