<?php

declare(strict_types=1);

namespace PHPSTORM_META {

    use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\BaseCreator;
    use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\OneAreaCreator;
    use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\TwoAreaCreator;

    override(\app(0), map([
        '' => '@',
    ]));

    override(\App::make(0), map([
        BaseCreator::class => BaseCreator::class,
        OneAreaCreator::class => OneAreaCreator::class,
        TwoAreaCreator::class => TwoAreaCreator::class,
    ]));

    override(\Illuminate\Contracts\Container\Container::make(0), map([
        BaseCreator::class => BaseCreator::class,
        OneAreaCreator::class => OneAreaCreator::class,
        TwoAreaCreator::class => TwoAreaCreator::class,
    ]));
}
