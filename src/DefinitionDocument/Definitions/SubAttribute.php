<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions;

/**
 * Class SubAttribute
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions
 */
class SubAttribute
{
    /**
     * @var array
     */
    protected $attributes = [];
    
    /**
     * @var string
     */
    protected $ruleMessage = '';
    
    /**
     * @var string
     */
    protected $mainKeyName = '';
    
    /**
     * get attribute
     *
     * @return array
     */
    public function attributes(): array
    {
        return $this->attributes;
    }
    
    /**
     * set attribute
     *
     * @param  string  $value
     * @param  string  $headerName
     */
    public function setAttributes(string $value, string $headerName): void
    {
        $this->attributes[$headerName] = $value;
    }
    
    /**
     * get rule message
     *
     * @return string
     */
    public function ruleMessage(): string
    {
        return $this->ruleMessage;
    }
    
    /**
     * set rule
     *
     * @param  string  $ruleMessage
     */
    public function setRuleMessage(string $ruleMessage): void
    {
        $this->ruleMessage = $ruleMessage;
    }
    
    /**
     * get main key name
     *
     * @return string
     */
    public function mainKeyName(): string
    {
        return $this->mainKeyName;
    }
    
    /**
     * set main key name
     *
     * @param  string  $mainKeyName
     */
    public function setMainKeyName(string $mainKeyName): void
    {
        $this->mainKeyName = $mainKeyName;
    }
}
