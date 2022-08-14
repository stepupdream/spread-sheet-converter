<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\Test;

use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Delete the directory for storing test results. Recursively delete a directory.
     */
    protected function resultReset(): void
    {
        $filesystem = $this->app->make(Filesystem::class);
        $filesystem->deleteDirectory(__DIR__.'/DefinitionDocument/Sample');
    }
}
