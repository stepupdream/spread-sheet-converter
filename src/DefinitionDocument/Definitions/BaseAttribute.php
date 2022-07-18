<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions;

use LogicException;

abstract class BaseAttribute
{
    /**
     * Get attribute by key.
     *
     * Get a string that indicates an array.
     * It can be obtained as an array.
     * Also, possible with a two-dimensional array.
     *
     * @param  array  $attributes
     * @param  string  $headerKey
     * @return array
     */
    protected function attributesByKey(array $attributes, string $headerKey): array
    {
        $attributesNotIndent = $this->attributeByKey($attributes, $headerKey);

        $attributesNotIndent = rtrim(ltrim($attributesNotIndent));
        if ($attributesNotIndent[0] !== '[' || ! str_ends_with($attributesNotIndent, ']')) {
            throw new LogicException("It's not a string that matches the array: ".$attributesNotIndent);
        }

        $attributesNotIndent = substr($attributesNotIndent, 1);
        $attributesNotIndent = substr($attributesNotIndent, 0, -1);
        $attributesNotIndent = rtrim(ltrim($attributesNotIndent));
        if ($attributesNotIndent[0] === '[' && str_ends_with($attributesNotIndent, ']')) {
            // ex:attributeNotLineArray)
            // [
            //   0 => ''
            //   1 => "1,2,3],"
            //   2 => "1,2,3]"
            // ]
            $attributeNotLineArray = explode('[', $attributesNotIndent);
            $elementCount = count($attributeNotLineArray);

            $result = [];
            for ($i = 1; $i < $elementCount; $i++) {
                $beforeConvertText = strstr($attributeNotLineArray[$i], ']', true);
                $result[] = explode(',', $beforeConvertText);
            }

            return $result;
        }

        return explode(',', $attributesNotIndent);
    }

    /**
     * Get attribute by key.
     *
     * @param  array  $attributes
     * @param  string  $headerKey
     * @return string
     */
    protected function attributeByKey(array $attributes, string $headerKey): string
    {
        return str_replace(PHP_EOL, '', $attributes[$headerKey]);
    }
}
