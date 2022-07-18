<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports;

use ErrorException;
use Illuminate\Filesystem\Filesystem;
use LogicException;

class FileOperation
{
    /**
     * Create the same file as the first argument at the position specified by the second argument.
     *
     * @param  string  $content
     * @param  string  $filePath
     * @param  bool  $isOverwrite
     */
    public function createFile(string $content, string $filePath, bool $isOverwrite = false): void
    {
        $dirPath = dirname($filePath);

        if (! is_dir($dirPath)) {
            $result = $this->makeDirectory($dirPath, 0777, true);
            if (! $result) {
                throw new LogicException($filePath.': Failed to make directory');
            }
        }

        if (! file_exists($filePath)) {
            $result = $this->put($filePath, $content);
            if (! $result) {
                throw new LogicException($filePath.': Failed to create');
            }

            return;
        }

        if ($isOverwrite) {
            // Hack:
            // An error occurred when overwriting, so always delete → create
            $result = $this->delete($filePath);
            if (! $result) {
                throw new LogicException($filePath.': Failed to delete');
            }

            $result = $this->put($filePath, $content);
            if (! $result) {
                throw new LogicException($filePath.': Failed to create by overwrite');
            }
        }
    }

    /**
     * Create a directory.
     *
     * @param  string  $path
     * @param  int  $mode
     * @param  bool  $recursive
     * @return bool
     * @see \Illuminate\Filesystem\Filesystem::makeDirectory
     */
    private function makeDirectory(string $path, int $mode = 0755, bool $recursive = false): bool
    {
        return mkdir($path, $mode, $recursive);
    }

    /**
     * Write the contents of a file.
     *
     * @param  string  $path
     * @param  string  $contents
     * @return int|bool
     * @see \Illuminate\Filesystem\Filesystem::put
     */
    private function put(string $path, string $contents): bool|int
    {
        return file_put_contents($path, $contents);
    }

    /**
     * Delete the file at a given path.
     *
     * @param  string|array  $paths
     * @return bool
     * @see \Illuminate\Filesystem\Filesystem::delete
     */
    private function delete(mixed $paths): bool
    {
        $paths = is_array($paths) ? $paths : func_get_args();

        $success = true;

        foreach ($paths as $path) {
            try {
                if (! @unlink($path)) {
                    $success = false;
                }
            } catch (ErrorException) {
                $success = false;
            }
        }

        return $success;
    }
}
