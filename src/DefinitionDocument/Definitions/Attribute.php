<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions;

/**
 * Class Attribute.
 */
class Attribute extends BaseAttribute
{
    /**
     * Array of contents by column.
     *
     * @var array
     */
    protected $attributeDetails = [];

    /**
     * Rule message text.
     *
     * @var string
     */
    protected $ruleMessage = '';

    /**
     * Get attribute detail.
     *
     * @return array
     */
    public function attributeDetails(): array
    {
        return $this->attributeDetails;
    }

    /**
     * Get attribute detail by header key.
     *
     * @param  string  $headerKey
     * @return mixed
     */
    public function getAttributeDetailByKey(string $headerKey)
    {
        return $this->getAttributeByKey($this->attributeDetails, $headerKey);
    }

    /**
     * Get attribute detail by header key.
     *
     * @param  string  $headerKey
     * @return array
     */
    public function getAttributeDetailArrayByKey(string $headerKey): array
    {
        return $this->getAttributeArrayByKey($this->attributeDetails, $headerKey);
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
