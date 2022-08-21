<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\Test;

use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use ReflectionClass;

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

    /**
     * Execute private function test.
     *
     * @param  object  $class
     * @param  string  $methodName
     * @param  array  $arguments
     * @return mixed
     */
    protected function executePrivateFunction(object $class, string $methodName, array $arguments): mixed
    {
        $reflection = new ReflectionClass($class);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($class, $arguments);
    }
}
