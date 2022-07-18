<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\Test\DefinitionDocument\Definitions;

use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute;
use StepUpDream\SpreadSheetConverter\Test\TestCase;

/**
 * Class AttributeTest.
 */
class AttributeTest extends TestCase
{
    /**
     * @test
     */
    public function getAttributeDetailByKey(): void
    {
        $attribute = new Attribute();
        $attribute->setAttributeDetails('a', 'test1');
        $attribute->setAttributeDetails(PHP_EOL.'b'.PHP_EOL, 'test2');
        $attribute->setAttributeDetails('[1,2,3]', 'test3');
        $attribute->setAttributeDetails('[[1,2,3],[1,2,3]]', 'test4');

        $response1 = $attribute->getAttributeDetailByKey('test1');
        $response2 = $attribute->getAttributeDetailByKey('test2');
        $response3 = $attribute->getAttributeDetailsByKey('test3');
        $response4 = $attribute->getAttributeDetailsByKey('test4');

        self::assertEquals('a', $response1);
        self::assertEquals('b', $response2);
        self::assertEquals([1, 2, 3], $response3);
        self::assertEquals([[1, 2, 3], [1, 2, 3]], $response4);
    }
}
