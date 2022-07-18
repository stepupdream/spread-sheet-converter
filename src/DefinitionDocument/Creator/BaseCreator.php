<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creator;

use LogicException;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports\SheetOperation;
use Str;

/**
 * Class BaseCreator
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Creator
 */
abstract class BaseCreator
{
    /**
     * @var \StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports\SheetOperation
     */
    protected $sheet_operation;
    
    /**
     * @var \StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation
     */
    protected $file_operation;
    
    /**
     * BaseCreator constructor.
     *
     * @param \StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports\FileOperation $file_operation
     * @param \StepUpDream\SpreadSheetConverter\SpreadSheetReader\Supports\SheetOperation $sheet_operation
     */
    public function __construct(
        FileOperation $file_operation,
        SheetOperation $sheet_operation
    ) {
        $this->sheet_operation = $sheet_operation;
        $this->file_operation = $file_operation;
    }
    
    /**
     * Generate a definition document
     *
     * @param \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definition\Attribute[] $attributes
     * @param string $use_blade
     * @param string|null $target_file_name
     * @param string $output_directory_path
     */
    protected function createDefinitionDocument(
        array $attributes,
        string $use_blade,
        ?string $target_file_name,
        string $output_directory_path
    ): void {
        foreach ($attributes as $attribute) {
            // If there is a specification to get only a part, skip other data
            if ($this->isReadSkip($attribute, $target_file_name)) {
                continue;
            }
            
            $target_path = $output_directory_path.DIRECTORY_SEPARATOR.Str::studly($attribute->sheetName()).DIRECTORY_SEPARATOR.$attribute->mainKeyName().'.yml';
            $blade_file = view('definition_document::'.Str::snake($use_blade),
                [
                    'attribute' => $attribute,
                ])->render();
            
            $this->file_operation->createFile($blade_file, $target_path, true);
        }
    }
    
    /**
     * Whether to skip reading
     *
     * @param \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definition\Attribute|\StepUpDream\SpreadSheetConverter\DefinitionDocument\Definition\ApiAttribute $attribute
     * @param string|null $target_file_name
     * @return bool
     */
    public function isReadSkip($attribute, ?string $target_file_name): bool
    {
        return $target_file_name !== null && Str::snake($target_file_name) !== Str::snake($attribute->mainKeyName());
    }
    
    /**
     * Verification of correct type specification
     *
     * @param \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definition\Attribute[] $attributes
     */
    protected function verifyDataTypeTable(array $attributes): void
    {
        foreach ($attributes as $attribute) {
            foreach ($attribute->subAttributes() as $sub_attribute) {
                $this->verifyDataTypeDetail($sub_attribute->attributes()['ColumnName'], $sub_attribute->attributes()['DataType']);
            }
        }
    }
    
    /**
     * Verification of correct type specification
     *
     * @param string $name
     * @param string $data_type
     */
    public function verifyDataTypeDetail(string $name, string $data_type): void
    {
        // Optional
    }
    
    /**
     * Verification of correct type specification
     *
     * @param \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definition\ApiAttribute[] $attributes
     */
    protected function verifyDataTypeForHttp(array $attributes): void
    {
        foreach ($attributes as $attribute) {
            foreach ($attribute->requestAttributes() as $sub_attribute) {
                $this->verifyDataTypeDetail($sub_attribute->attributes()['ColumnName'], $sub_attribute->attributes()['DataType']);
            }
            foreach ($attribute->responseAttributes() as $sub_attribute) {
                $this->verifyDataTypeDetail($sub_attribute->attributes()['ColumnName'], $sub_attribute->attributes()['DataType']);
            }
        }
    }
    
    /**
     * Validate header name is correct
     *
     * @param array $header_names_sub
     */
    protected function verifyHeaderName(array $header_names_sub): void
    {
        if (empty($header_names_sub['ColumnName']) || empty($header_names_sub['DataType'])) {
            throw new LogicException('ColumnType and ColumnName data could not be read');
        }
    }
    
    /**
     * Validate header name is correct
     *
     * @param array $header_names_sub
     */
    protected function verifyHeaderNameForHttp(array $header_names_sub): void
    {
        if (empty($header_names_sub['ColumnType'] || empty($header_names_sub['ColumnName']) || empty($header_names_sub['DataType']))) {
            throw new LogicException('ColumnType and ColumnName and DataType data could not be read');
        }
    }
}
