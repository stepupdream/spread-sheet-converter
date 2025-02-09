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
     * @param string[] $attributes
     * @return mixed[]
     */
    protected function attributeJsonByKey(array $attributes, string $headerKey): array
    {
        $attributeNotIndent = $this->attributeByKey($attributes, $headerKey);
        if ($attributeNotIndent === '') {
            throw new LogicException('Failed to convert from JSON to array.');
        }

        $decodedText = json_decode($attributeNotIndent, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($decodedText)) {
            throw new LogicException('Failed to convert from JSON to array.');
        }

        return $decodedText;
    }

    /**
     * Get attribute by key.
     *
     * @param string[] $attributes
     */
    protected function attributeByKey(array $attributes, string $headerKey): string
    {
        if (!array_key_exists($headerKey, $attributes)) {
            return '';
        }

        return str_replace(PHP_EOL, '', $attributes[$headerKey]);
    }
}
