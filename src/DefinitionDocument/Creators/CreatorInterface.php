<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators;

use Illuminate\Console\OutputStyle;

interface CreatorInterface
{
    /**
     * Set the output implementation that should be used by the console.
     *
     * @return $this
     */
    public function setOutput(OutputStyle $output): static;

    /**
     * Execution of processing.
     */
    public function run(?string $targetFileName): void;
}
