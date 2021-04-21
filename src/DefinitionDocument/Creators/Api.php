<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators;

use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ApiAttribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\SubAttribute;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports\Facades\SpreadSheetReader;
use Str;

/**
 * Class Api
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators
 */
class Api extends BaseCreator
{
    /**
     * Execution of processing
     *
     * @param  string  $categoryName
     * @param  string  $useBlade
     * @param  string  $sheetId
     * @param  string  $outputDirectoryPath
     * @param  string|null  $targetFileName
     */
    public function run(
        string $categoryName,
        string $useBlade,
        string $sheetId,
        string $outputDirectoryPath,
        string $targetFileName = null
    ): void {
        $convertedSheetData = [];
        $spreadSheets = SpreadSheetReader::read($sheetId);
        $requestRuleSheetName = config('spread_sheet.request_rule_sheet_name');
        $requestRuleSheet = SpreadSheetReader::read($sheetId, $requestRuleSheetName);
        
        foreach ($spreadSheets as $sheetName => $sheet) {
            if ($sheetName === $requestRuleSheetName) {
                continue;
            }
            
            $convertedSheetData[] = $this->convertApiSheetData($sheet, $requestRuleSheet, Str::studly($categoryName), $sheetName);
        }
        
        // Return to one dimension because it is a multi-dimensional array of sheets
        $this->createDefinitionDocument(collect($convertedSheetData)->flatten()->all(), $useBlade, $targetFileName, $outputDirectoryPath);
    }
    
    /**
     * Convert spreadsheet data
     *
     * @param  array  $sheet
     * @param  array  $requestRuleSheet
     * @param  string  $categoryName
     * @param  string  $sheetName
     * @return Attribute[]
     */
    protected function convertApiSheetData(array $sheet, array $requestRuleSheet, string $categoryName, string $sheetName): array
    {
        $rowNumber = 0;
        $convertedSheetData = [];
        
        while (!empty($sheet[$rowNumber])) {
            if ($this->sheetOperation->isAllEmpty($sheet[$rowNumber])) {
                $rowNumber++;
                continue;
            }
            
            $convertedSheetData[] = $this->createApiAttribute($sheet, $requestRuleSheet, $categoryName, $rowNumber, $sheetName);
        }
    
        return $convertedSheetData;
    }
    
    /**
     * Generate Attribute class based on Sheet data
     *
     * @param  array  $sheet
     * @param  array  $requestRuleSheet
     * @param  string  $spreadsheetCategoryName
     * @param  int  $rowNumber
     * @param  string  $sheetName
     * @return \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ApiAttribute
     */
    protected function createApiAttribute(
        array $sheet,
        array $requestRuleSheet,
        string $spreadsheetCategoryName,
        int &$rowNumber,
        string $sheetName
    ): ApiAttribute {
        $headerNamesMain = $this->sheetOperation->getMainAttributeKeyName($sheet);
        $headerNamesSub = $this->sheetOperation->getSubAttributeKeyName($sheet);
        $ruleColumnName = config('spread_sheet.request_rule_column_name');
        $this->verifyHeaderNameForHttp($headerNamesSub);
        $requestAttributes = [];
        $responseAttributes = [];
        
        $attribute = new ApiAttribute($spreadsheetCategoryName, $sheetName);
        $attribute->setMainKeyName(collect($sheet[$rowNumber])->first());
        foreach ($headerNamesMain as $headerNameMain) {
            $attribute->setAttributes($sheet[$rowNumber][$headerNameMain], $headerNameMain);
        }

        // Request layer
        while (!empty($sheet[$rowNumber]) && $sheet[$rowNumber][$headerNamesSub['ColumnType']] !== 'Response') {
            // Processing ends when the column name is blank or blank from the beginning
            if ($sheet[$rowNumber][$headerNamesSub['ColumnName']] === '') {
                $rowNumber++;
                break;
            }
            
            $requestAttribute = new SubAttribute();
            $requestAttribute->setMainKeyName($sheet[$rowNumber][$headerNamesSub['ColumnType']]);
            foreach ($headerNamesSub as $headerNameSub) {
                $requestAttribute->setAttributes($sheet[$rowNumber][$headerNameSub], $headerNameSub);
            }
            $requestAttributes[] = $requestAttribute;
            
            // add rule message
            if (!empty($sheet[$rowNumber][$ruleColumnName])) {
                $message = $this->createRuleMessage($sheet, $requestRuleSheet, $rowNumber, $ruleColumnName);
                $requestAttribute->setRuleMessage($message);
            }
            $rowNumber++;
        }
        $attribute->setRequestAttributes($requestAttributes);
        
        // Response layer
        while (!empty($sheet[$rowNumber]) && !$this->sheetOperation->isAllEmpty($sheet[$rowNumber])) {
            // Processing ends when the column name is blank or blank from the beginning
            if ($sheet[$rowNumber][$headerNamesSub['ColumnName']] === '') {
                $rowNumber++;
                break;
            }
            
            $responseAttribute = new SubAttribute();
            $responseAttribute->setMainKeyName(collect($sheet[$rowNumber][$headerNamesSub['ColumnType']])->first());
            foreach ($headerNamesSub as $headerNameSub) {
                $responseAttribute->setAttributes($sheet[$rowNumber][$headerNameSub], $headerNameSub);
            }
            $responseAttributes[] = $responseAttribute;
            $rowNumber++;
        }
        $attribute->setResponseAttributes($responseAttributes);
        
        return $attribute;
    }
    
    /**
     * Generate rule message
     *
     * @param  array  $sheet
     * @param  array  $sheetRequestRule
     * @param  int  $rowNumber
     * @param  string  $ruleColumnName
     * @return string
     */
    protected function createRuleMessage(array $sheet, array $sheetRequestRule, int $rowNumber, string $ruleColumnName): string
    {
        $message = '';
        $rules = explode('|', $sheet[$rowNumber][$ruleColumnName]);
        
        foreach ($rules as $rule) {
            $rule = trim($rule);
            $ruleMessage = collect($sheetRequestRule)->first(function ($value) use ($rule) {
                    return $value['ruleDataType'] === trim($rule);
                })['ruleMessage'] ?? null;
            
            if (empty($ruleMessage)) {
                continue;
            }
            
            if ($message === '') {
                $message .= "'$rule': '$ruleMessage'";
            } else {
                $message .= ", '$rule': '$ruleMessage'";
            }
        }
        
        return '{'.$message.'}';
    }
}
