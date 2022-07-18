<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Readers;

use File;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\YamlFileOperation;

/**
 * Class BaseReader
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Readers
 */
class BaseReader
{
    /**
     * @var \StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\YamlFileOperation
     */
    protected $yamlFileOperation;
    
    /**
     * @var \StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation
     */
    protected $fileOperation;
    
    /**
     * BaseReader constructor.
     *
     * @param  \StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation  $fileOperation
     * @param  \StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\YamlFileOperation  $yamlFileOperation
     */
    public function __construct(
        FileOperation $fileOperation,
        YamlFileOperation $yamlFileOperation
    ) {
        $this->yamlFileOperation = $yamlFileOperation;
        $this->fileOperation = $fileOperation;
    }
    
    /**
     * Reading definition data
     *
     * @param  string  $targetDirectoryPath
     * @return array
     */
    public function readByDirectoryPath(string $targetDirectoryPath): array
    {
        if (!File::isDirectory($targetDirectoryPath)) {
            return [];
        }
        
        $filePaths = $this->fileOperation->getAllFilePath($targetDirectoryPath);
        
        return $this->yamlFileOperation->parseAllYaml($filePaths);
    }
    
    /**
     * Reading definition data
     *
     * @param  string  $filePath
     * @return array
     */
    public function readByFilePath(string $filePath): array
    {
        return $this->yamlFileOperation->parseYaml($filePath);
    }
}
