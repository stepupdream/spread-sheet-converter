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
     * @param  string[]  $attributes
     * @param  string  $headerKey
     * @return mixed[]
     */
    protected function attributesByKey(array $attributes, string $headerKey): array
    {
        $attributeNotIndent = $this->attributeByKey($attributes, $headerKey);

        $attributeNotIndent = rtrim(ltrim($attributeNotIndent));
        if ($attributeNotIndent[0] !== '[' || ! str_ends_with($attributeNotIndent, ']')) {
            throw new LogicException("It's not a string that matches the array: ".$attributeNotIndent);
        }

        $attributeNotIndent = substr($attributeNotIndent, 1);
        $attributeNotIndent = substr($attributeNotIndent, 0, -1);
        $attributeNotIndent = rtrim(ltrim($attributeNotIndent));
        if ($attributeNotIndent[0] === '[' && str_ends_with($attributeNotIndent, ']')) {
            // ex:attributeNotLineArray)
            // [
            //   0 => ''
            //   1 => "1,2,3],"
            //   2 => "1,2,3]"
            // ]
            $attributeNotLineArray = explode('[', $attributeNotIndent);
            $elementCount = count($attributeNotLineArray);

            $result = [];
            for ($i = 1; $i < $elementCount; $i++) {
                $beforeConvertText = strstr($attributeNotLineArray[$i], ']', true);
                if (! $beforeConvertText) {
                    throw new LogicException("It's not a string that matches the array: ".$attributeNotIndent);
                }

                $result[] = explode(',', $beforeConvertText);
            }

            return $result;
        }

        return explode(',', $attributeNotIndent);
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
