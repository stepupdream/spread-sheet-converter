<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports;

use Illuminate\Filesystem\Filesystem;
use LogicException;

/**
 * Class FileOperation
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports
 */
class FileOperation
{
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $file;
    
    /**
     * FileOperation constructor.
     */
    public function __construct(
        Filesystem $file
    ) {
        $this->file = $file;
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
        
        if (!is_dir($dirPath)) {
            $result = $this->file->makeDirectory($dirPath, 0777, true);
            if (!$result) {
                throw new LogicException($filePath.'');
            }
        }
        
        if (!file_exists($filePath)) {
            $result = $this->file->put($filePath, $content);
            if (!$result) {
                throw new LogicException($filePath.': Failed to create');
            }
            return;
        }
        
        if ($isOverwrite && file_exists($filePath)) {
            // Hack:
            // An error occurred when overwriting, so always delete → create
            $this->file->delete($filePath);
            $result = $this->file->put($filePath, $content);
            if (!$result) {
                throw new LogicException($filePath.': Failed to create');
            }
        }
    }
}
