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
     * Parse Yaml files
     *
     * @param string $file_path
     * @return mixed
     */
    public function parseYaml(string $file_path)
    {
        $extension = File::extension($file_path);
        
        if ($extension !== 'yml') {
            throw new LogicException('Could not parse because it is not Yaml data file_path: ' . $file_path);
        }
        
        return Yaml::parse(file_get_contents($file_path));
    }
    
    /**
     * Parse all definition Yaml files
     *
     * @param array $file_paths
     * @return mixed
     */
    public function parseAllYaml(array $file_paths)
    {
        $yaml_parse_texts = [];
    
        foreach ($file_paths as $file_path => $file_path) {
            if (count($this->parseYaml($file_path)) >= 2) {
                throw new LogicException('Yaml data must be one data per file file_path: ' . $file_path);
            }
            
            // Rule that there is always one data in Yaml data
            $yaml_parse_texts[$file_path] = collect($this->parseYaml($file_path))->first();
        }
        
        return $yaml_parse_texts;
    }
}
