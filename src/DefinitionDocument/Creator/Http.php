<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creator;

use SpreadSheetReader;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definition\ApiAttribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definition\Attribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definition\SubAttribute;
use Str;

/**
 * Class Http
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Creator
 */
class Http extends BaseCreator
{
    /**
     * Execution of processing
     *
     * @param string $category_name
     * @param string $use_blade
     * @param string $sheet_id
     * @param string $output_directory_path
     * @param string|null $target_file_name
     */
    public function run(
        string $category_name,
        string $use_blade,
        string $sheet_id,
        string $output_directory_path,
        string $target_file_name = null
    ): void {
        $converted_sheet_data = [];
        $spread_sheets = SpreadSheetReader::read($sheet_id);
        $request_rule_sheet_name = config('spread_sheet.request_rule_sheet_name');
        $request_rule_sheet = SpreadSheetReader::read($sheet_id, $request_rule_sheet_name);
        
        foreach ($spread_sheets as $sheet_name => $sheet) {
            if ($sheet_name === $request_rule_sheet_name) {
                continue;
            }
            
            $converted_sheet_data[] = $this->convertSheetData($sheet, $request_rule_sheet, Str::studly($category_name), $sheet_name);
        }
        
        // Return to one dimension because it is a multi-dimensional array of sheets
        $this->createDefinitionDocument(collect($converted_sheet_data)->flatten()->all(), $use_blade, $target_file_name,
            $output_directory_path);
    }
    
    /**
     * Convert spreadsheet data
     *
     * @param array $sheet
     * @param array $request_rule_sheet
     * @param string $category_name
     * @param string $sheet_name
     * @return Attribute[]
     */
    protected function convertSheetData(array $sheet, array $request_rule_sheet, string $category_name, string $sheet_name): array
    {
        $row_number = 0;
        $converted_sheet_data = [];
        
        while (!empty($sheet[$row_number])) {
            if ($this->sheet_operation->isAllEmpty($sheet[$row_number])) {
                $row_number++;
                continue;
            }
            
            $converted_sheet_data[] = $this->createHttpAttribute($sheet, $request_rule_sheet, $category_name, $row_number, $sheet_name);
        }
        
        return $converted_sheet_data;
    }
    
    /**
     * Generate Attribute class based on Sheet data
     *
     * @param array $sheet
     * @param array $request_rule_sheet
     * @param string $spreadsheet_category_name
     * @param int $row_number
     * @param string $sheet_name
     * @return \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definition\ApiAttribute
     */
    protected function createHttpAttribute(
        array $sheet,
        array $request_rule_sheet,
        string $spreadsheet_category_name,
        int &$row_number,
        string $sheet_name
    ): ApiAttribute {
        $header_names_main = $this->sheet_operation->getMainAttributeKeyName($sheet);
        $header_names_sub = $this->sheet_operation->getSubAttributeKeyName($sheet);
        $rule_column_name = config('spread_sheet.request_rule_column_name');
        $this->verifyHeaderNameForHttp($header_names_sub);
        $request_attributes = [];
        $response_attributes = [];
        
        $attribute = new ApiAttribute($spreadsheet_category_name, $sheet_name);
        $attribute->setMainKeyName(collect($sheet[$row_number])->first());
        foreach ($header_names_main as $header_name_main) {
            $attribute->setAttributes($sheet[$row_number][$header_name_main], $header_name_main);
        }
        
        // Request layer
        while (!empty($sheet[$row_number]) && $sheet[$row_number][$header_names_sub['ColumnType']] !== 'Response') {
            // Processing ends when the column name is blank or blank from the beginning
            if ($sheet[$row_number][$header_names_sub['ColumnName']] === '') {
                $row_number++;
                break;
            }
            
            $request_attribute = new SubAttribute();
            $request_attribute->setMainKeyName($sheet[$row_number][$header_names_sub['ColumnType']]);
            foreach ($header_names_sub as $header_name_sub) {
                $request_attribute->setAttributes($sheet[$row_number][$header_name_sub], $header_name_sub);
            }
            $request_attributes[] = $request_attribute;
            
            // add rule message
            if (!empty($sheet[$row_number][$rule_column_name])) {
                $message = $this->createRuleMessage($sheet, $request_rule_sheet, $row_number, $rule_column_name);
                $request_attribute->setRuleMessage($message);
            }
            $row_number++;
        }
        $attribute->setRequestAttributes($request_attributes);
        
        // Response layer
        while (!empty($sheet[$row_number]) && !$this->sheet_operation->isAllEmpty($sheet[$row_number])) {
            // Processing ends when the column name is blank or blank from the beginning
            if ($sheet[$row_number][$header_names_sub['ColumnName']] === '') {
                $row_number++;
                break;
            }
            
            $response_attribute = new SubAttribute();
            $response_attribute->setMainKeyName(collect($sheet[$row_number][$header_names_sub['ColumnType']])->first());
            foreach ($header_names_sub as $header_name_sub) {
                $response_attribute->setAttributes($sheet[$row_number][$header_name_sub], $header_name_sub);
            }
            $response_attributes[] = $response_attribute;
            $row_number++;
        }
        $attribute->setResponseAttributes($response_attributes);
        
        return $attribute;
    }
    
    /**
     * Generate rule message
     *
     * @param array $sheet
     * @param array $sheet_request_rule
     * @param int $row_number
     * @param string $rule_column_name
     * @return string
     */
    protected function createRuleMessage(array $sheet, array $sheet_request_rule, int $row_number, string $rule_column_name): string
    {
        $message = '';
        $rules = explode('|', $sheet[$row_number][$rule_column_name]);
        
        foreach ($rules as $rule) {
            $rule = (string)trim($rule);
            $rule_message = collect($sheet_request_rule)->first(function ($value) use ($rule) {
                    return $value['ruleDataType'] === trim($rule);
                })['ruleMessage'] ?? null;
            
            if (empty($rule_message)) {
                continue;
            }
            
            if ($message === '') {
                $message .= "'{$rule}': '{$rule_message}'";
            } else {
                $message .= ", '{$rule}': '{$rule_message}'";
            }
        }
        
        return '{'.$message.'}';
    }
}
