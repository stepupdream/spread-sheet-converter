<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions;

use LogicException;

abstract class BaseAttribute
{
    /**
     * Get attribute by key (json).
     *
     * Get a string that indicates an array.
     * It can be obtained as an array.
     *
     * @param  string[]  $attributes
     * @param  string  $headerKey
     * @return mixed[]
     */
    protected function attributeJsonByKey(array $attributes, string $headerKey): array
    {
        $attributeNotIndent = $this->attributeByKey($attributes, $headerKey);

        $decodedText = json_decode($attributeNotIndent, true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($decodedText)) {
            throw new LogicException('Failed to convert from json to array.');
        }

        return $decodedText;
    }

    /**
     * Get attribute by key.
     *
     * @param  string[]  $attributes
     * @param  string  $headerKey
     * @return string
     */
    protected function attributeByKey(array $attributes, string $headerKey): string
    {
        return str_replace(PHP_EOL, '', $attributes[$headerKey]);
    }
}
