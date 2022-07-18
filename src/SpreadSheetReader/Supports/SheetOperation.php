<?php

namespace StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports;

use LogicException;

/**
 * Class SheetOperation
 *
 * @package StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports
 */
class SheetOperation
{
    /**
     * @var array the first row in an array
     */
    protected $main_attribute_key_name = [];
    
    /**
     * @var array the first row in an array
     */
    protected $sub_attribute_key_name = [];
    
    /**
     * Make the first row the key of the associative array
     *
     * @param $sheet_values
     * @param $target_sheet
     * @return array
     */
    public function getTitleArray($sheet_values, $target_sheet): array
    {
        $result = [];
        $header_row = [];
        $is_header = true;
        
        if (empty($sheet_values)) {
            throw new LogicException('need sheet header: '.$target_sheet);
        }
        
        foreach ($sheet_values as $row) {
            if ($is_header) {
                $header_row = $row;
                $is_header = false;
            } else {
                $row_with_key = [];
                
                foreach ($header_row as $key => $value) {
                    $row_with_key[$value] = $row[$key] ?? '';
                }
                
                $result[] = $row_with_key;
            }
        }
        
        return $result;
    }
    
    /**
     * Get the first row in an array
     *
     * @param array $sheet
     * @return string[] Sheet header list
     */
    public function getMainAttributeKeyName(array $sheet): array
    {
        $sheet_first_row = collect($sheet)->first();
        $names = [];
        
        $cache_key = collect($sheet_first_row)->first();
        
        if (!empty($this->main_attribute_key_name[$cache_key])) {
            return $this->main_attribute_key_name[$cache_key];
        }
        
        foreach ($sheet_first_row as $key => $value) {
            if ($key === '') {
                break;
            }
            $names[] = $key;
        }
        
        $this->main_attribute_key_name[$cache_key] = $names;
        
        return $names;
    }
    
    /**
     * Get the first row in an array
     *
     * @param array $sheet
     * @return string[] Sheet header list
     */
    public function getSubAttributeKeyName(array $sheet): array
    {
        $sheet_first_row = collect($sheet)->first();
        $should_add_start = false;
        $names = [];
        
        $cache_key = collect($sheet_first_row)->first();
        
        if (!empty($this->sub_attribute_key_name[$cache_key])) {
            return $this->sub_attribute_key_name[$cache_key];
        }
        
        // Get what's to the right of the blank part of the header row in Spreadsheet
        foreach ($sheet_first_row as $key => $value) {
            if ($key === '') {
                $should_add_start = true;
                continue;
            }
            if ($should_add_start) {
                $names[$key] = $key;
            }
        }
        
        $this->sub_attribute_key_name[$cache_key] = $names;
        
        return $names;
    }
    
    /**
     * Whether the entire row is all empty
     *
     * @param array $values
     * @return bool
     */
    public function isAllEmpty(array $values): bool
    {
        foreach ($values as $value) {
            if ($value !== '') {
                return false;
            }
        }
        
        return true;
    }
}
