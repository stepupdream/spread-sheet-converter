<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports;

use File;
use LogicException;

/**
 * Class FileOperation
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports
 */
class FileOperation
{
    /**
     * Recursively get a list of file paths from a directory
     *
     * @param  string  $directoryPath
     * @return array
     */
    public function getAllFilePath(string $directoryPath): array
    {
        $filePaths = [];
        
        if (!File::isDirectory($directoryPath)) {
            throw new LogicException('Not a Directory');
        }
        
        $files = File::allFiles($directoryPath);
        foreach ($files as $file) {
            $realPath = (string) $file->getRealPath();
            $filePaths[$realPath] = $realPath;
        }
        
        return $filePaths;
    }
    
    /**
     * Create the same file as the first argument at the position specified by the second argument
     *
     * @param  string  $content
     * @param  string  $filePath
     * @param  bool  $isOverwrite
     */
    public function createFile(string $content, string $filePath, bool $isOverwrite = false): void
    {
        $dirPath = dirname($filePath);
        
        if (!File::isDirectory($dirPath)) {
            $result = File::makeDirectory($dirPath, 0777, true);
            if (!$result) {
                throw new LogicException($filePath.'');
            }
        }
        
        if (!File::exists($filePath)) {
            $result = File::put($filePath, $content);
            if (!$result) {
                throw new LogicException($filePath.': Failed to create');
            }
            return;
        }
        
        if ($isOverwrite && File::exists($filePath)) {
            // Hack:
            // An error occurred when overwriting, so always delete → create
            File::delete($filePath);
            $result = File::put($filePath, $content);
            if (!$result) {
                throw new LogicException($filePath.': Failed to create');
            }
        }
    }
}
