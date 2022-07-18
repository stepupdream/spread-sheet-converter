<?php

namespace StepUpDream\SpreadSheetConverter\SpreadSheetReader\Reader;

use Google_Client;
use Google_Exception;
use Google_Service_Sheets;
use Google_Service_Sheets_Sheet;
use LogicException;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports\SheetOperation;

/**
 * Class BaseReader
 *
 * @package StepUpDream\SpreadSheetConverter\SpreadSheetReader\Reader
 */
class BaseReader
{
    /**
     * @var \StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports\SheetOperation
     */
    protected $sheet_operation;
    
    /**
     * BaseReader constructor.
     *
     * @param \StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports\SheetOperation $sheet_operation
     */
    public function __construct(
        SheetOperation $sheet_operation
    ) {
        $this->sheet_operation = $sheet_operation;
    }
    
    /**
     * Read spreadsheet data
     *
     * @param string $sheet_id
     * @param string|null $target_sheet_name
     * @return array Table information array containing information for each sheet：key is sheet name
     */
    public function read(string $sheet_id, string $target_sheet_name = null) : array
    {
        $credentials_path = config('spread_sheet.credentials_path');
        
        $client = new Google_Client();
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        try {
            $client->setAuthConfig($credentials_path);
        } catch (Google_Exception $e) {
            throw new LogicException('Failed to get information from spreadsheet');
        }
        
        $sheets = new Google_Service_Sheets($client);
        $spreadsheets = [];
        
        /** @var Google_Service_Sheets_Sheet $sheet */
        foreach ($sheets->spreadsheets->get($sheet_id)->getSheets() as $sheet) {
            $target_sheet = $sheet->getProperties()->getTitle();
            $sheet_data_range = $sheets->spreadsheets_values->get($sheet_id, $target_sheet);
            $spreadsheets[$target_sheet] = $this->sheet_operation->getTitleArray($sheet_data_range->getValues(), $target_sheet);
        }
        
        if (isset($target_sheet_name)) {
            if (empty($spreadsheets[$target_sheet_name])) {
                throw new LogicException('can not read sheet data: ' . $target_sheet_name);
            }
            return $spreadsheets[$target_sheet_name];
        }
        
        return $spreadsheets;
    }
}
