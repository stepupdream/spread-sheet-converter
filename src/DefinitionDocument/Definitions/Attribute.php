<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions;

class Attribute extends BaseAttribute
{
    /**
     * One line of information in the child element part.
     *
     * @var string[]
     */
    protected array $attributeDetails = [];

    /**
     * Rule message.
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
     */
    public function getAttributeDetailByKey(string $headerKey): string
    {
        return $this->attributeByKey($this->attributeDetails, $headerKey);
    }

    /**
     * Get attribute detail by header key.
     *
     * @return mixed[]
     */
    public function getAttributeDetailJsonByKey(string $headerKey): array
    {
        return $this->attributeJsonByKey($this->attributeDetails, $headerKey);
    }

    /**
     * Set attribute details.
     */
    public function setAttributeDetails(string $value, string $headerName): void
    {
        $this->attributeDetails[$headerName] = $value;
    }

    /**
     * Unset attribute detail.
     */
    public function unsetAttributeDetail(string $headerName): void
    {
        unset($this->attributeDetails[$headerName]);
    }

    /**
     * Get rule message.
     *
     * @noinspection PhpUnused
     */
    public function ruleMessage(): string
    {
        return $this->ruleMessage;
    }

    /**
     * Set rule message.
     */
    public function setRuleMessage(string $ruleMessage): void
    {
        $this->ruleMessage = $ruleMessage;
    }
}
