<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators;

use Illuminate\Support\Str;
use LogicException;

class MultiGroup extends Base
{
    /**
     * A cache of the request rule sheet.
     *
     * @var string[][]
     */
    protected array $requestRuleSheet = [];

    /**
     * Execution of processing.
     *
     * @param  string|null  $targetFileName
     */
    public function run(?string $targetFileName): void
    {
        $requestRuleSheetName = config('stepupdream.spread-sheet-converter.request_rule_sheet_name');
        $spreadSheets = $this->spreadSheetReader->read($this->sheetId);
        foreach ($spreadSheets as $sheetName => $sheet) {
            if (! empty($requestRuleSheetName) && $sheetName === $requestRuleSheetName) {
                continue;
            }

            $parentAttributes = $this->convertSheetData($sheet, Str::studly($this->categoryName), $sheetName);
            $this->verifySheetData($parentAttributes);
            $this->createDefinitionDocument($parentAttributes, $targetFileName);
        }
    }

    /**
     * Generate rule message.
     *
     * @param  string[][]  $sheet
     * @param  int  $rowNumber
     * @return string
     */
    protected function createRuleMessage(array $sheet, int $rowNumber): string
    {
        $ruleColumnName = $this->ruleColumnName();

        if (empty($sheet[$rowNumber][$ruleColumnName])) {
            return '';
        }

        $requestRuleSheetName = $this->requestRuleSheetName();
        if ($this->requestRuleSheet === []) {
            $this->requestRuleSheet = $this->spreadSheetReader->readBySheetName($this->sheetId, $requestRuleSheetName);
        }

        $message = '';
        $rules = explode('|', $sheet[$rowNumber][$ruleColumnName]);

        foreach ($rules as $rule) {
            $rule = trim($rule);
            $ruleMessageFind = collect($this->requestRuleSheet)->first(function ($value) use ($rule) {
                return $value['ruleDataType'] === trim($rule);
            });
            $ruleMessage = $ruleMessageFind['ruleMessage'] ?? null;

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

    /**
     * Rule column name.
     *
     * @return string
     */
    protected function ruleColumnName(): string
    {
        $ruleColumnName = config('stepupdream.spread-sheet-converter.request_rule_column_name');

        if (! is_string($ruleColumnName) || $ruleColumnName === '') {
            throw new LogicException('The name of the rule colum name is incorrect.');
        }

        return $ruleColumnName;
    }

    /**
     * Request rule sheet name.
     *
     * @return string
     */
    protected function requestRuleSheetName(): string
    {
        $requestRuleSheetName = config('stepupdream.spread-sheet-converter.request_rule_sheet_name');

        if (! is_string($requestRuleSheetName) || $requestRuleSheetName === '') {
            throw new LogicException('The name of the request rule sheet is incorrect.');
        }

        return $requestRuleSheetName;
    }
}
