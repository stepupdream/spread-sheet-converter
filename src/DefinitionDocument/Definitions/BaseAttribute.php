<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions;

/**
 * Class BaseAttribute.
 */
abstract class BaseAttribute
{
    /**
     * Get attribute by key.
     *
     * @param  array  $attributes
     * @param  string  $headerKey
     * @return mixed
     */
    protected function getAttributeByKey(array $attributes, string $headerKey)
    {
        return str_replace(PHP_EOL, '', $attributes[$headerKey]);
    }

    /**
     * Get attribute by key.
     *
     * @param  array  $attributes
     * @param  string  $headerKey
     * @return array
     */
    protected function getAttributeArrayByKey(array $attributes, string $headerKey): array
    {
        $attributeNotLine = $this->getAttributeByKey($attributes, $headerKey);

        $attributeTrim = rtrim(ltrim($attributeNotLine));
        if ($attributeTrim[0] === '[' && $attributeTrim[strlen($attributeTrim) - 1] === ']') {
            return explode(',', str_replace(['[', ']'], '', $attributeTrim));
        }

        return explode(',', $attributeTrim);
    }
}
