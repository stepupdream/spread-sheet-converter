<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\interface;

/**
 * Interface CreatorInterface
 */
interface CreatorInterface
{
    /**
     * Execution of processing.
     */
    public function run(?string $targetFileName): void;
}
