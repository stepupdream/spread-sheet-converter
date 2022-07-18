<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions;

class Attribute extends BaseAttribute
{
    /**
     * @var string[]
     */
    protected array $attributeDetails = [];

    /**
     * @var string
     */
    protected string $ruleMessage = '';

    /**
     * Get attributeDetail.
     *
     * @return string[]
     */
    public function attributeDetails(): array
    {
        return $this->attributeDetails;
    }

    /**
     * Get attribute detail by header key.
     *
     * @param  string  $headerKey
     * @return string
     */
    public function getAttributeDetailByKey(string $headerKey): string
    {
        return $this->attributeByKey($this->attributeDetails, $headerKey);
    }

    /**
     * Get attribute detail by header key.
     *
     * @param  string  $headerKey
     * @return mixed[]
     */
    public function getAttributeDetailsByKey(string $headerKey): array
    {
        return $this->attributesByKey($this->attributeDetails, $headerKey);
    }

    /**
     * Set attribute details.
     *
     * @param  string  $value
     * @param  string  $headerName
     */
    public function setAttributeDetails(string $value, string $headerName): void
    {
        $this->attributeDetails[$headerName] = $value;
    }

    /**
     * Unset attribute detail.
     *
     * @param  string  $headerName
     */
    public function unsetAttributeDetail(string $headerName): void
    {
        unset($this->attributeDetails[$headerName]);
    }

    /**
     * Get rule message.
     *
     * @return string
     */
    public function ruleMessage(): string
    {
        return $this->ruleMessage;
    }

    /**
     * Set rule message.
     *
     * @param  string  $ruleMessage
     */
    public function setRuleMessage(string $ruleMessage): void
    {
        $this->ruleMessage = $ruleMessage;
    }
}
