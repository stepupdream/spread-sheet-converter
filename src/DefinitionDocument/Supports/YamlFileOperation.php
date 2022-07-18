<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports;

use File;
use LogicException;
use Yaml;

/**
 * Class YamlFileOperation
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports
 */
class YamlFileOperation
{
    /**
     * Parse all definition Yaml files
     *
     * @param  array  $filePaths
     * @return mixed
     */
    public function parseAllYaml(array $filePaths): array
    {
        $yamlParseTexts = [];
        
        foreach ($filePaths as $filePath) {
            if (count($this->parseYaml($filePath)) >= 2) {
                throw new LogicException('Yaml data must be one data per file filePath: '.$filePath);
            }
            
            // Rule that there is always one data in Yaml data
            $yamlParseTexts[$filePath] = collect($this->parseYaml($filePath))->first();
        }
        
        return $yamlParseTexts;
    }
    
    /**
     * Parse Yaml files
     *
     * @param  string  $filePath
     * @return mixed
     */
    public function parseYaml(string $filePath)
    {
        $extension = File::extension($filePath);
        
        if ($extension !== 'yml') {
            throw new LogicException('Could not parse because it is not Yaml data filePath: '.$filePath);
        }
        
        return Yaml::parse(file_get_contents($filePath));
    }
}
