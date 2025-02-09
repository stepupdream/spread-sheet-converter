<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\Test\DefinitionDocument\Definitions;

use Exception;
use JsonException;
use LogicException;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\Attribute;

beforeEach(function () {
    $this->attribute = new Attribute();
});

describe('attributeDetails', function () {
    it('returns an empty array by default', function () {
        expect($this->attribute->attributeDetails())->toBeArray()
            ->toBeEmpty();
    });

    it('adds and retrieves an attribute detail using a header key', function () {
        $headerKey = 'header1';
        $value = 'value1';

        $this->attribute->setAttributeDetails($value, $headerKey);

        expect($this->attribute->attributeDetails())->toHaveKey($headerKey, $value);
    });

    it('remove an attribute detail using a header key', function () {
        $headerKey = 'header1';
        $value = 'value1';

        $this->attribute->setAttributeDetails($value, $headerKey);
        $this->attribute->unsetAttributeDetail($headerKey);

        expect($this->attribute->attributeDetails())->not->toHaveKey($headerKey);
    });
});

describe('getAttributeDetailByKey', function () {
    it('returns the correct value for a valid header key', function () {
        $headerKey = 'header1';
        $value = 'value1';

        $this->attribute->setAttributeDetails($value, $headerKey);

        expect($this->attribute->getAttributeDetailByKey($headerKey))->toBe($value);
    });
});

describe('getAttributeDetailJsonByKey', function () {
    it('retrieves JSON data for a valid key', function () {
        $headerKey = 'header1';
        $value = json_encode(['key1' => 'value1', 'key2' => 'value2'], JSON_THROW_ON_ERROR);

        $this->attribute->setAttributeDetails($value, $headerKey);

        expect($this->attribute->getAttributeDetailJsonByKey($headerKey))->toBeArray()
            ->toMatchArray(['key1' => 'value1', 'key2' => 'value2']);
    });

    it('throws an exception if the value is not valid JSON', function () {
        $headerKey = 'header1';
        $invalidValue = 'invalid_json';

        $this->attribute->setAttributeDetails($invalidValue, $headerKey);

        expect(fn() => $this->attribute->getAttributeDetailJsonByKey($headerKey))
            ->toThrow(JsonException::class);
    });

    it('throw an exception when trying to retrieve JSON for a non-existent key.', function () {
        expect(fn() => $this->attribute->getAttributeDetailJsonByKey('non_existent'))
            ->toThrow(LogicException::class);
    });
});

describe('ruleMessage', function () {
    it('returns an empty string by default', function () {
        expect($this->attribute->ruleMessage())->toBe('');
    });

    it('allows setting and retrieving the rule message', function () {
        $message = 'This is a rule message';

        $this->attribute->setRuleMessage($message);

        expect($this->attribute->ruleMessage())->toBe($message);
    });
});

describe('setAttributeDetails', function () {
    it('overrides an existing attribute detail with the same header key', function () {
        $headerKey = 'header1';
        $value1 = 'value1';
        $value2 = 'value2';

        $this->attribute->setAttributeDetails($value1, $headerKey);
        $this->attribute->setAttributeDetails($value2, $headerKey);

        expect($this->attribute->attributeDetails())->toHaveKey($headerKey, $value2);
    });
});

describe('unsetAttributeDetail', function () {
    it('does not throw an error when unsetting a non-existent key', function () {
        expect(fn() => $this->attribute->unsetAttributeDetail('non_existent'))->not->toThrow(Exception::class);
    });
});
