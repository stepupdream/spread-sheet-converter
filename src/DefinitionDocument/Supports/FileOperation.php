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
     * @param string $directory_path directory path
     * @return array file path list
     */
    public function getAllFilePath(string $directory_path)
    {
        $file_paths = [];
        
        if (!File::isDirectory($directory_path)) {
            throw new LogicException('Not a Directory');
        }
        
        $files = File::allFiles($directory_path);
        foreach ($files as $file) {
            $file_paths[$file->getRealPath()] = $file->getRealPath();
        }
        
        return $file_paths;
    }
    
    /**
     * Create the same file as the first argument at the position specified by the second argument
     *
     * @param string $content
     * @param string $file_path
     * @param bool $is_overwrite
     */
    public function createFile(string $content, string $file_path, bool $is_overwrite = false)
    {
        $dir_path = dirname($file_path);
        
        if (!File::isDirectory($dir_path)) {
            $result = File::makeDirectory($dir_path, 0777, true);
            if (!$result) {
                throw new LogicException($file_path . '');
            }
        }
        
        if (!File::exists($file_path)) {
            $result = File::put($file_path, $content);
            if (!$result) {
                throw new LogicException($file_path . ': Failed to create');
            }
            return;
        }
        
        if ($is_overwrite && File::exists($file_path)) {
            // Hack:
            // An error occurred when overwriting, so always delete → create
            File::delete($file_path);
            $result = File::put($file_path, $content);
            if (!$result) {
                throw new LogicException($file_path . ': Failed to create');
            }
        }
    }
}
