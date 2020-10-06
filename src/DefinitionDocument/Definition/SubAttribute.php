<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Definition;

/**
 * Class SubAttribute
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Definition
 */
class SubAttribute
{
    /**
     * @var string
     */
    protected $attributes = [];
    
    /**
     * @var string
     */
    protected $rule_message = '';
    
    /**
     * @var string
     */
    protected $main_key_name = '';
    
    /**
     * get attribute
     *
     * @return string
     */
    public function attributes()
    {
        return $this->attributes;
    }
    
    /**
     * set attribute
     *
     * @param string $value
     * @param string $header_name
     */
    public function setAttributes(string $value, string $header_name)
    {
        $this->attributes[$header_name] = $value;
    }
    
    /**
     * get rule_message
     *
     * @return string
     */
    public function ruleMessage() : string
    {
        return $this->rule_message;
    }
    
    /**
     * set rule
     *
     * @param string $rule_message
     */
    public function setRuleMessage(string $rule_message)
    {
        $this->rule_message = $rule_message;
    }
    
    /**
     * get main_key_name
     *
     * @return string
     */
    public function mainKeyName() : string
    {
        return $this->main_key_name;
    }
    
    /**
     * set main_key_name
     *
     * @param string $main_key_name
     */
    public function setMainKeyName(string $main_key_name)
    {
        $this->main_key_name = $main_key_name;
    }
}
