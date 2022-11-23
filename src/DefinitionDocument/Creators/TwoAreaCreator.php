<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators;

use Illuminate\Console\View\Components\Info;
use Illuminate\Support\Str;
use LogicException;
use StepUpDream\DreamAbilitySupport\Supports\File\FileOperation;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute;
use StepUpDream\SpreadSheetConverter\SpreadSheetService\Readers\SpreadSheetReader;

class TwoAreaCreator extends Base
{
    /**
     * A cache of the request rule sheet.
     *
     * @var string[][]
     */
    protected array $requestRuleSheet = [];

    /**
     * Delimiter column header name.
     *
     * @var string
     */
    protected string $separationKey;

    /**
     * Key name of the group.
     *
     * @var string
     */
    protected string $attributeGroupColumnName;

    /**
     * BaseCreator constructor.
     *
     * @param  \StepUpDream\DreamAbilitySupport\Supports\File\FileOperation  $fileOperation
     * @param  \StepUpDream\SpreadSheetConverter\SpreadSheetService\Readers\SpreadSheetReader  $spreadSheetReader
     * @param  string[]  $readSpreadSheet
     */
    public function __construct(
        protected FileOperation $fileOperation,
        protected SpreadSheetReader $spreadSheetReader,
        array $readSpreadSheet
    ) {
        $this->separationKey = $readSpreadSheet['separation_key'];
        $this->attributeGroupColumnName = $readSpreadSheet['attribute_group_column_name'] ?? '';

        parent::__construct($fileOperation, $spreadSheetReader, $readSpreadSheet);
    }

    /**
     * Execution of processing.
     *
     * @param  string|null  $targetFileName
     */
    public function run(?string $targetFileName): void
    {
        $requestRuleSheetName = config('stepupdream.spread-sheet-converter.request_rule_sheet_name');
        $spreadSheets = $this->spreadSheetReader->read($this->sheetId);
        $spreadSheetTitle = $this->spreadSheetReader->spreadSheetTitle($this->sheetId);
        (new Info($this->output))->render(sprintf('%s file load', $spreadSheetTitle));

        foreach ($spreadSheets as $sheetName => $sheet) {
            if (! empty($requestRuleSheetName) && $sheetName === $requestRuleSheetName) {
                continue;
            }

            $parentAttributes = $this->convertSheetData($sheet, Str::studly($spreadSheetTitle), $sheetName);
            $this->verifySheetData($parentAttributes);
            $this->fileOperation->createGitKeep($this->definitionDirectoryPath);
            $this->createDefinitionDocument($parentAttributes, $targetFileName);
        }

        $this->output->newLine();
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

    /**
     * Generate Attribute class based on Sheet data.
     *
     * @param  string[][]  $sheet
     * @param  string  $spreadsheetTitle
     * @param  int  $rowNumber
     * @param  string  $sheetName
     * @return \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute
     */
    protected function createParentAttribute(
        array $sheet,
        string $spreadsheetTitle,
        int &$rowNumber,
        string $sheetName
    ): ParentAttribute {
        $headerNamesParent = $this->spreadSheetReader->getParentAttributeKeyName($sheet, $this->separationKey);
        $headerNamesChild = $this->spreadSheetReader->getAttributeKeyName($sheet, $this->separationKey);
        $mainKeyNameChild = collect($headerNamesChild)->first();

        $parentAttribute = new ParentAttribute($spreadsheetTitle, $sheetName, $headerNamesChild);
        foreach ($headerNamesParent as $headerNameParent) {
            $parentAttribute->setParentAttributeDetails($sheet[$rowNumber][$headerNameParent], $headerNameParent);
        }

        while (! empty($sheet[$rowNumber]) && ! $this->spreadSheetReader->isAllEmpty($sheet[$rowNumber])) {
            $groupKeyName = $sheet[$rowNumber][$mainKeyNameChild];
            $attributes = $this->createAttributesGroup($sheet, $rowNumber, $headerNamesChild);

            if (empty($this->attributeGroupColumnName)) {
                $parentAttribute->setAttributesGroup($attributes);
            } else {
                $parentAttribute->setAttributesGroup($attributes, $groupKeyName);
            }
        }

        return $parentAttribute;
    }

    /**
     * Create only one attributes group.
     *
     * @param  string[][]  $sheet
     * @param  int  $rowNumber
     * @param  string[]  $headerNames
     * @return \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute[]
     */
    protected function createAttributesGroup(array $sheet, int &$rowNumber, array $headerNames): array
    {
        $attributes = [];
        $mainKeyName = (string) collect($headerNames)->first();
        $beforeMainKeyData = $sheet[$rowNumber][$headerNames[$mainKeyName]];

        while (true) {
            $attribute = new Attribute();
            foreach ($headerNames as $headerName) {
                $attribute->setAttributeDetails($sheet[$rowNumber][$headerName], $headerName);
            }

            $attribute->unsetAttributeDetail($this->attributeGroupColumnName);
            $message = $this->createRuleMessage($sheet, $rowNumber);
            $attribute->setRuleMessage($message);

            if (! $this->spreadSheetReader->isAllEmpty($attribute->attributeDetails())) {
                $attributes[] = $attribute;
            }
            $rowNumber++;

            if (empty($sheet[$rowNumber]) || $this->spreadSheetReader->isAllEmpty($sheet[$rowNumber])) {
                break;
            }

            // If the key of the group is switched, it is judged that the group is finished
            if (! empty($this->attributeGroupColumnName) &&
                $sheet[$rowNumber][$headerNames[$mainKeyName]] !== '' &&
                $sheet[$rowNumber][$headerNames[$mainKeyName]] !== $beforeMainKeyData) {
                break;
            }
        }

        return $attributes;
    }

    /**
     * File output destination.
     *
     * @param  \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute  $parentAttribute
     * @return string
     */
    protected function outputPath(ParentAttribute $parentAttribute): string
    {
        $mainKeyName = collect($parentAttribute->parentAttributeDetails())->first();

        if ($mainKeyName === null) {
            throw new LogicException('mainKeyName was not found');
        }

        $fileName = sprintf('%s.%s', $mainKeyName, $this->fileExtension);

        return $this->outputDirectoryPath.
            DIRECTORY_SEPARATOR.
            Str::studly($parentAttribute->sheetName()).
            DIRECTORY_SEPARATOR.$fileName;
    }
}
