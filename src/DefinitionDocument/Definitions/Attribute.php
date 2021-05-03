<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions;

/**
 * Class Attribute
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions
 */
class Attribute
{
    /**
     * @var array
     */
    protected $attributeDetails = [];
    
    /**
     * @var string
     */
    protected $ruleMessage = '';
    
    /**
     * Get attribute detail
     *
     * @return array
     */
    public function attributeDetails(): array
    {
        return $this->attributeDetails;
    }
    
    /**
     * Set attribute details
     *
     * @param  string  $value
     * @param  string  $headerName
     */
    public function setAttributeDetails(string $value, string $headerName): void
    {
        $this->attributeDetails[$headerName] = $value;
    }
    
    /**
     * Unset attribute detail
     *
     * @param  string  $headerName
     */
    public function unsetAttributeDetail(string $headerName): void
    {
        unset($this->attributeDetails[$headerName]);
    }
    
    /**
     * Get rule message
     *
     * @return string
     */
    public function ruleMessage(): string
    {
        return $this->ruleMessage;
    }
    
    /**
     * Set rule message
     *
     * @param  string  $ruleMessage
     */
    public function setRuleMessage(string $ruleMessage): void
    {
        $this->ruleMessage = $ruleMessage;
    }
}
