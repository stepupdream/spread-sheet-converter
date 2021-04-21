<?php

namespace StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports;

use LogicException;

/**
 * class SheetOperation
 *
 * @package StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports
 */
class SheetOperation
{
    /**
     * @var array the first row in an array
     */
    protected $mainAttributeKeyName = [];
    
    /**
     * @var array the first row in an array
     */
    protected $subAttributeKeyName = [];
    
    /**
     * Make the first row the key of the associative array
     *
     * @param  array  $sheetValues
     * @param  string  $targetSheet
     * @return array
     */
    public function getTitleArray(array $sheetValues, string $targetSheet): array
    {
        $result = [];
        $headerRow = [];
        $isHeader = true;
        
        if (empty($sheetValues)) {
            throw new LogicException('need sheet header: '.$targetSheet);
        }
        
        foreach ($sheetValues as $row) {
            if ($isHeader) {
                $headerRow = $row;
                $isHeader = false;
            } else {
                $rowWithKey = [];
                foreach ($headerRow as $key => $value) {
                    $rowWithKey[$value] = $row[$key] ?? '';
                }
                
                $result[] = $rowWithKey;
            }
        }
        
        return $result;
    }
    
    /**
     * Get the first row in an array
     *
     * @param  array  $sheet
     * @return string[] Sheet header list
     */
    public function getMainAttributeKeyName(array $sheet): array
    {
        $sheetFirstRow = collect($sheet)->first();
        $names = [];
        
        $cacheKey = collect($sheetFirstRow)->first();
        
        if (!empty($this->mainAttributeKeyName[$cacheKey])) {
            return $this->mainAttributeKeyName[$cacheKey];
        }
        
        foreach ($sheetFirstRow as $key => $value) {
            if ($key === '') {
                break;
            }
            $names[$key] = $key;
        }
        
        $this->mainAttributeKeyName[$cacheKey] = $names;
        
        return $names;
    }
    
    /**
     * Get the first row in an array
     *
     * @param  array  $sheet
     * @return string[] Sheet header list
     */
    public function getSubAttributeKeyName(array $sheet): array
    {
        $sheetFirstRow = collect($sheet)->first();
        $shouldAddStart = false;
        $names = [];
        
        $cacheKey = collect($sheetFirstRow)->first();
        
        if (!empty($this->subAttributeKeyName[$cacheKey])) {
            return $this->subAttributeKeyName[$cacheKey];
        }
        
        // Get what's to the right of the blank part of the header row in Spreadsheet
        foreach ($sheetFirstRow as $key => $value) {
            if ($key === '') {
                $shouldAddStart = true;
                continue;
            }
            if ($shouldAddStart) {
                $names[$key] = $key;
            }
        }
        
        $this->subAttributeKeyName[$cacheKey] = $names;
        
        return $names;
    }
    
    /**
     * Whether the entire row is all empty
     *
     * @param  array  $values
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
