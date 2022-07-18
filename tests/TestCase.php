<?php

namespace StepUpDream\SpreadSheetConverter\Test;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use ReflectionClass;

/**
 * Class TestCase.
 */
abstract class TestCase extends OrchestraTestCase
{
    /**
     * Execute private function test.
     *
     * @param $class
     * @param  string  $methodName
     * @param  array  $arguments
     * @return mixed
     */
    protected function executePrivateFunction($class, string $methodName, array $arguments)
    {
        $reflection = new ReflectionClass($class);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($class, $arguments);
    }
}
