<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators;

use Illuminate\Console\OutputStyle;
use LogicException;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\Struct\SpreadSheetConfig;

/**
 * Class CreatorFactory.
 */
readonly class CreatorFactory
{
    /**
     * Constructor for the class.
     *
     * @param OutputStyle $output An instance used for styling console output.
     * @return void
     */
    public function __construct(private OutputStyle $output)
    {
    }

    /**
     * Creates and returns an instance of a creator object based on the 'read_type' provided in the input array.
     */
    public function make(SpreadSheetConfig $spreadSheetConfig): BaseCreator
    {
        $attributesGroupCreator = app()->make(AttributeGroupCreator::class, ['spreadSheetConfig' => $spreadSheetConfig]);

        return match ($spreadSheetConfig->readType()) {
            'OneArea' => app()->make(BaseCreator::class, [
                'spreadSheetConfig' => $spreadSheetConfig,
                'output' => $this->output,
                'areaCreator' => app()->make(
                    OneAreaCreator::class,
                    [
                        'attributeGroupCreator' => $attributesGroupCreator,
                        'spreadSheetConfig' => $spreadSheetConfig,
                    ],
                ),
            ]),
            'TwoArea' => app()->make(BaseCreator::class, [
                'spreadSheetConfig' => $spreadSheetConfig,
                'output' => $this->output,
                'areaCreator' => app()->make(
                    TwoAreaCreator::class,
                    [
                        'attributeGroupCreator' => $attributesGroupCreator,
                        'spreadSheetConfig' => $spreadSheetConfig,
                    ],
                ),
            ]),
            default => throw new LogicException('There were no matching conditions'),
        };
    }
}
