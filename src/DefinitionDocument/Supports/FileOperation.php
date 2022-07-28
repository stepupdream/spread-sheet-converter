<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports;

use ErrorException;
use Illuminate\Filesystem\Filesystem;
use LogicException;
use Symfony\Component\Finder\Finder;

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
     * Create git keep file.
     */
    public function createGitKeep(string $directoryPath): void
    {
        $allFiles = $this->allFiles($directoryPath);
        if (empty($allFiles)) {
            $this->createFile('gitkeep', $directoryPath.'/.gitkeep');
        }
    }

    /**
     * Check if it is the same as the file that already exists.
     *
     * @param  string  $content
     * @param  string  $targetDirectoryPath
     * @param  string  $fileName
     * @return bool
     */
    public function shouldCreate(string $content, string $targetDirectoryPath, string $fileName): bool
    {
        if (! is_dir($targetDirectoryPath)) {
            return true;
        }

        $allFiles = $this->allFiles($targetDirectoryPath, true);
        foreach ($allFiles as $allFile) {
            if ($allFile->getFilename() === $fileName) {
                return file_get_contents($allFile->getRealPath()) !== $content;
            }
        }

        return true;
    }

    /**
     * Get all the files from the given directory (recursive).
     *
     * @param  string  $directory
     * @param  bool  $hidden
     * @return \Symfony\Component\Finder\SplFileInfo[]
     * @see \Illuminate\Filesystem\Filesystem::allFiles
     */
    public function allFiles(string $directory, bool $hidden = false): array
    {
        return iterator_to_array(
            Finder::create()->files()->ignoreDotFiles(! $hidden)->in($directory)->sortByName(),
            false
        );
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
     * @param  string|string[]  $paths
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
