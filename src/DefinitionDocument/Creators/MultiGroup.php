<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators;

use Str;

/**
 * Class MultiGroup
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators
 */
class MultiGroup extends Base
{
    /**
     * @var array
     */
    protected $requestRuleSheet;
    
    /**
     * Execution of processing
     *
     * @param  string|null  $targetFileName
     */
    public function run(string $targetFileName = null): void
    {
        $convertedSheetData = [];
        $requestRuleSheetName = config('step_up_dream.spread_sheet_converter.request_rule_sheet_name');
        $spreadSheets = $this->spreadSheetReader->read($this->sheetId);
        foreach ($spreadSheets as $sheetName => $sheet) {
            if (!empty($requestRuleSheetName) && $sheetName === $requestRuleSheetName) {
                continue;
            }
            
            $convertedSheetData[] = $this->convertSheetData($sheet, Str::studly($this->categoryName), $sheetName);
        }
        
        // Return to one dimension because it is a multi-dimensional array of sheets
        $parentAttributes = collect($convertedSheetData)->flatten()->all();
        $this->verifySheetData($parentAttributes);
        $this->createDefinitionDocument($parentAttributes, $targetFileName);
    }
    
    /**
     * Generate rule message
     *
     * @param  array  $sheet
     * @param  int  $rowNumber
     * @return string
     */
    protected function createRuleMessage(array $sheet, int $rowNumber): string
    {
        $requestRuleSheetName = config('step_up_dream.spread_sheet_converter.request_rule_sheet_name');
        $ruleColumnName = config('step_up_dream.spread_sheet_converter.request_rule_column_name');
        
        if (empty($sheet[$rowNumber][$ruleColumnName])) {
            return '';
        }
        
        if ($this->requestRuleSheet === null) {
            $this->requestRuleSheet = $this->spreadSheetReader->read($this->sheetId, $requestRuleSheetName);
        }
        
        $message = '';
        $rules = explode('|', $sheet[$rowNumber][$ruleColumnName]);
        
        foreach ($rules as $rule) {
            $rule = trim($rule);
            $ruleMessage = collect($this->requestRuleSheet)->first(function ($value) use ($rule) {
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
