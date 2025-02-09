<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators;

use LogicException;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\Struct\SpreadSheetConfig;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute;
use StepUpDream\SpreadSheetConverter\SpreadSheetService\Readers\SpreadSheetReader;

readonly class AttributeGroupCreator
{
    /**
     * Request Rule Sheet.
     *
     * @var string[][]
     */
    protected array $requestRuleSheet;

    /**
     * BaseCreator constructor.
     */
    public function __construct(
        private SpreadSheetReader $spreadSheetReader,
        private SpreadSheetConfig $spreadSheetConfig,
    ) {
    }

    /**
     * Create only one attributes group.
     *
     * @param string[][] $sheet
     * @param string[] $headerNames
     * @return Attribute[]
     */
    public function createAttributesGroup(array $sheet, int &$rowNumber, array $headerNames): array
    {
        $attributes = [];
        $mainKeyName = (string)collect($headerNames)->first();
        $beforeMainKeyData = $sheet[$rowNumber][$headerNames[$mainKeyName]];

        while (true) {
            $attribute = new Attribute();
            foreach ($headerNames as $headerName) {
                $attribute->setAttributeDetails($sheet[$rowNumber][$headerName], $headerName);
            }

            $attribute->unsetAttributeDetail($this->spreadSheetConfig->attributeGroupColumnName());
            $message = $this->createRuleMessage($sheet, $rowNumber);
            $attribute->setRuleMessage($message);

            if (!$this->spreadSheetReader->isRowEmpty($attribute->attributeDetails())) {
                $attributes[] = $attribute;
            }
            $rowNumber++;

            if (empty($sheet[$rowNumber]) || $this->spreadSheetReader->isRowEmpty($sheet[$rowNumber])) {
                break;
            }

            // If the key of the group is switched, it is judged that the group is finished.
            if (!empty($this->attributeGroupColumnName) &&
                $sheet[$rowNumber][$headerNames[$mainKeyName]] !== '' &&
                $sheet[$rowNumber][$headerNames[$mainKeyName]] !== $beforeMainKeyData) {
                break;
            }
        }

        return $attributes;
    }

    /**
     * Generate rule message.
     *
     * @param string[][] $sheet
     */
    private function createRuleMessage(array $sheet, int $rowNumber): string
    {
        $ruleColumnName = $this->ruleColumnName();

        if (empty($sheet[$rowNumber][$ruleColumnName])) {
            return '';
        }

        if ($this->requestRuleSheet === []) {
            $requestRuleSheetName = $this->requestRuleSheetName();
            $this->requestRuleSheet = $this->spreadSheetReader->readBySheetName($this->spreadSheetConfig->sheetId(), $requestRuleSheetName);
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

        return '{' . $message . '}';
    }

    /**
     * Rule column name.
     */
    private function ruleColumnName(): string
    {
        $ruleColumnName = config('stepupdream.spread-sheet-converter.request_rule_column_name');

        if (!is_string($ruleColumnName) || $ruleColumnName === '') {
            throw new LogicException('The name of the rule colum name is incorrect.');
        }

        return $ruleColumnName;
    }

    /**
     * Request rule sheet name.
     */
    private function requestRuleSheetName(): string
    {
        $requestRuleSheetName = config('stepupdream.spread-sheet-converter.request_rule_sheet_name');

        if (!is_string($requestRuleSheetName) || $requestRuleSheetName === '') {
            throw new LogicException('The name of the request rule sheet is incorrect.');
        }

        return $requestRuleSheetName;
    }
}
