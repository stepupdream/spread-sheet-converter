<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Reader;

use File;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\YamlFileOperation;

/**
 * Class BaseReader
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Reader
 */
class BaseReader
{
    /**
     * @var \StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\YamlFileOperation
     */
    protected $yaml_file_operation;
    
    /**
     * @var \StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation
     */
    protected $file_operation;
    
    /**
     * BaseReader constructor.
     *
     * @param \StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation $file_operation
     * @param \StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\YamlFileOperation $yaml_file_operation
     */
    public function __construct(
        FileOperation $file_operation,
        YamlFileOperation $yaml_file_operation
    ) {
        $this->yaml_file_operation = $yaml_file_operation;
        $this->file_operation = $file_operation;
    }
    
    /**
     * Reading definition data
     *
     * @param string $target_directory_path
     * @return array|mixed
     */
    public function readByDirectoryPath(string $target_directory_path): array
    {
        if (!File::isDirectory($target_directory_path)) {
            return [];
        }
        
        $file_paths = $this->file_operation->getAllFilePath($target_directory_path);
        
        return $this->yaml_file_operation->parseAllYaml($file_paths);
    }
    
    /**
     * Reading definition data
     *
     * @param string $file_path
     * @return array|mixed
     */
    public function readByFilePath(string $file_path): array
    {
        return $this->yaml_file_operation->parseYaml($file_path);
    }
}
