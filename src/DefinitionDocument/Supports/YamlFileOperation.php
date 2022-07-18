<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports;

use LogicException;
use Symfony\Component\Yaml\Yaml;

class YamlFileOperation
{
    /**
     * Parse all definition Yaml files.
     *
     * @param  array  $filePaths
     * @return array
     */
    public function parseAllYaml(array $filePaths): array
    {
        $yamlParseTexts = [];

        foreach ($filePaths as $filePath) {
            $yaml = $this->parseYaml($filePath);
            if (count($yaml) !== 1) {
                throw new LogicException('Yaml data must be one data per file filePath: '.$filePath);
            }

            // Rule that there is always one data in Yaml data
            $yamlParseTexts[$filePath] = reset($yaml);
        }

        return $yamlParseTexts;
    }

    /**
     * Parse Yaml files.
     *
     * @param  string  $filePath
     * @return mixed
     */
    public function parseYaml(string $filePath): mixed
    {
        $extension = $this->extension($filePath);

        if ($extension !== 'yml') {
            throw new LogicException('Could not parse because it is not Yaml data filePath: '.$filePath);
        }

        $contents = file_get_contents($filePath);
        if (! $contents) {
            throw new LogicException('Failed to get the file :'.$filePath);
        }

        return Yaml::parse($contents);
    }

    /**
     * Extract the file extension from a file path.
     *
     * @param  string  $path
     * @return string
     * @see \Illuminate\Filesystem\Filesystem::extension
     */
    private function extension(string $path): string
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }
}
