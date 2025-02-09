<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\Struct;

use LogicException;

class SpreadSheetConfig
{
    /**
     * loading type
     */
    private string $readType;

    /**
     * file extension
     */
    private string $fileExtension;

    /**
     * file classification name
     */
    private string $categoryTag;

    /**
     * definition file
     */
    private string $definitionDirectoryPath;

    /**
     * output destination
     */
    private string $outputDirectoryPath;

    /**
     * sheet id
     */
    private string $sheetId;

    /**
     * blade file to use
     */
    private string $useBladeFileName;

    /**
     * Name of the key to which the grouping is based.
     */
    private string $attributeGroupColumnName;

    /**
     * The name of the column that is branched.
     */
    private string $separationKey;

    /**
     * Constructor.
     *
     * @param array<string, mixed> $readSpreadSheet
     */
    public function __construct(
        public array $readSpreadSheet,
    ) {
        $this->verifyKey($readSpreadSheet);

        $this->sheetId = (string)$readSpreadSheet['sheet_id'];
        $this->categoryTag = $readSpreadSheet['category_tag'];
        $this->readType = $readSpreadSheet['read_type'];
        $this->useBladeFileName = $readSpreadSheet['use_blade_file_name'];
        $this->outputDirectoryPath = $readSpreadSheet['output_directory_path'];
        $this->definitionDirectoryPath = $readSpreadSheet['definition_directory_path'];
        $this->fileExtension = $readSpreadSheet['file_extension'];
        $this->attributeGroupColumnName = $readSpreadSheet['attribute_group_column_name'];
        $this->separationKey = $readSpreadSheet['separation_key'];
    }

    /**
     * Verify the existence of the key.
     *
     * @param array<string, mixed> $readSpreadSheet
     */
    private function verifyKey(array $readSpreadSheet): void
    {
        $keys = [
            'sheet_id',
            'category_tag',
            'read_type',
            'use_blade_file_name',
            'output_directory_path',
            'definition_directory_path',
            'file_extension',
            'attribute_group_column_name',
        ];

        foreach ($keys as $key) {
            if (!array_key_exists($key, $readSpreadSheet)) {
                throw new LogicException('There is no required setting value:' . $key);
            }
        }
    }

    /**
     * Get useBladeFileName.
     */
    public function useBladeFileName(): string
    {
        return $this->useBladeFileName;
    }

    /**
     * Get sheetId.
     */
    public function sheetId(): string
    {
        return $this->sheetId;
    }

    /**
     * Get outputDirectoryPath.
     */
    public function outputDirectoryPath(): string
    {
        return $this->outputDirectoryPath;
    }

    /**
     * Get definitionDirectoryPath.
     */
    public function definitionDirectoryPath(): string
    {
        return $this->definitionDirectoryPath;
    }

    /**
     * Get categoryTag.
     */
    public function categoryTag(): string
    {
        return $this->categoryTag;
    }

    /**
     * Get fileExtension.
     */
    public function fileExtension(): string
    {
        return $this->fileExtension;
    }

    /**
     * Get readType.
     */
    public function readType(): string
    {
        return $this->readType;
    }

    /**
     * Get attributeGroupColumnName.
     */
    public function attributeGroupColumnName(): string
    {
        return $this->attributeGroupColumnName;
    }

    /**
     * Get separationKey.
     */
    public function separationKey(): string
    {
        return $this->separationKey;
    }
}
