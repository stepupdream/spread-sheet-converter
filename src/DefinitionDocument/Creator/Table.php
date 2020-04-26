<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creator;

use SpreadSheetReader;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definition\Attribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definition\SubAttribute;
use Str;

/**
 * Class Table
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Creator
 */
class Table extends BaseCreator
{
    /**
     * Execution of processing
     *
     * @param string $category_name
     * @param string $sheet_id
     * @param string $output_directory_path
     * @param string $target_file_name
     */
    public function run(string $category_name, string $sheet_id, string $output_directory_path, string $target_file_name = null)
    {
        $converted_sheet_data = [];
        $spread_sheets = SpreadSheetReader::read($sheet_id);
        
        foreach ($spread_sheets as $sheet_name => $sheet) {
            $converted_sheet_data[] = $this->convertSheetData($sheet, Str::studly($category_name), $sheet_name);
        }
        
        // Return to one dimension because it is a multi-dimensional array of sheets
        $attributes = collect($converted_sheet_data)->flatten()->all();
        $this->verifyDataTypeTable($attributes);
        $this->createDefinitionDocument($attributes, $target_file_name, $category_name, $output_directory_path);
    }
    
    /**
     * Convert spreadsheet data
     *
     * @param array $sheet
     * @param string $category_name
     * @param string $sheet_name
     * @return \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definition\Attribute[]
     */
    protected function convertSheetData(array $sheet, string $category_name, string $sheet_name)
    {
        $row_number = 0;
        $converted_sheet_data = [];
        
        while (!empty($sheet[$row_number])) {
            if ($this->sheet_operation->isAllEmpty($sheet[$row_number])) {
                $row_number++;
                continue;
            }
            
            $converted_sheet_data[] = $this->createTableAttribute($sheet, $category_name, $row_number, $sheet_name);
        }
        
        return $converted_sheet_data;
    }
    
    /**
     * Generate Attribute class based on Sheet data
     *
     * @param array $sheet
     * @param string $spreadsheet_category_name
     * @param int $row_number
     * @param int $sheet_name
     * @return \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definition\Attribute
     */
    protected function createTableAttribute(array $sheet, string $spreadsheet_category_name, int &$row_number, string $sheet_name)
    {
        $header_names_main = $this->sheet_operation->getMainAttributeKeyName($sheet);
        $header_names_sub = $this->sheet_operation->getSubAttributeKeyName($sheet);
        $this->verifyHeaderName($header_names_sub);

        $sub_attributes = [];
        
        $attribute = new Attribute($spreadsheet_category_name);
        $attribute->setMainKeyName(collect($sheet[$row_number])->first());
        foreach ($header_names_main as $header_name_main) {
            $attribute->setAttributes($sheet[$row_number][$header_name_main], $header_name_main, $sheet_name);
        }
        
        while (!empty($sheet[$row_number]) && !$this->sheet_operation->isAllEmpty($sheet[$row_number])) {
            $sub_attribute = new SubAttribute();
            $sub_attribute->setMainKeyName($sheet[$row_number][$header_names_sub['ColumnName']]);
            foreach ($header_names_sub as $header_name_sub) {
                $sub_attribute->setAttributes($sheet[$row_number][$header_name_sub], $header_name_sub);
            }
            $sub_attributes[] = $sub_attribute;
            $row_number++;
        }
        $attribute->setSubAttributes($sub_attributes);
        
        return $attribute;
    }
}
