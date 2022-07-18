<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Console;

use LogicException;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\MultiGroup;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\Other;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\SingleGroup;

class DefinitionDocumentCommand extends BaseCreateCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spread_sheet_converter:create_definition_document {--category=} {--file_name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create definition document {any:category} {any:file_name}';

    /**
     * Run command.
     */
    public function handle(): void
    {
        $targetCategory = $this->categoryOption();
        $targetFileName = $this->fileNameOption();
        $readSpreadSheets = $this->readSpreadSheets();

        foreach ($readSpreadSheets as $readSpreadSheet) {
            if (! empty($targetCategory) && $targetCategory !== $readSpreadSheet['category_name']) {
                continue;
            }

            /** @var \StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\Base $creator */
            $creator = match ($readSpreadSheet['read_type']) {
                'SingleGroup' => app()->make(SingleGroup::class, ['readSpreadSheet' => $readSpreadSheet]),
                'MultiGroup'  => app()->make(MultiGroup::class, ['readSpreadSheet' => $readSpreadSheet]),
                'Other'       => app()->make(Other::class, ['readSpreadSheet' => $readSpreadSheet]),
                default       => throw new LogicException('There were no matching conditions'),
            };
            $creator->run($targetFileName);
            $this->info('Completed: '.$readSpreadSheet['category_name']);
        }
    }

    /**
     * category
     *
     * @return string|null
     */
    private function categoryOption(): string|null
    {
        $category = $this->option('category');
        if ($category === null) {
            return null;
        }

        if (is_string($category)) {
            return $category;
        }

        throw new LogicException('The option specification is incorrect: category');
    }

    /**
     * file name
     *
     * @return string|null
     */
    private function fileNameOption(): string|null
    {
        $category = $this->option('file_name');
        if ($category === null) {
            return null;
        }

        if (is_string($category)) {
            return $category;
        }

        throw new LogicException('The option specification is incorrect: file_name');
    }

    /**
     * read Spread Sheets
     *
     * @return mixed[][]
     */
    private function readSpreadSheets(): array
    {
        $readSpreadSheets = config('step_up_dream.spread_sheet_converter.read_spread_sheets');

        if (! is_array($readSpreadSheets) || ! $this->isMultidimensional($readSpreadSheets)) {
            throw new LogicException('Must be a two-dimensional array:read_spread_sheets');
        }

        foreach ($readSpreadSheets as $readSpreadSheet) {
            $this->verifyKey($readSpreadSheet);
        }

        return $readSpreadSheets;
    }

    /**
     * Whether it is a multidimensional array.
     *
     * @param  mixed[]  $array
     * @return bool
     */
    public function isMultidimensional(array $array): bool
    {
        return count($array) !== count($array, 1);
    }

    /**
     * verify the existence of the key.
     *
     * @param  mixed[]  $readSpreadSheet
     * @return void
     */
    private function verifyKey(array $readSpreadSheet): void
    {
        $keys = [
            'sheet_id',
            'category_name',
            'read_type',
            'use_blade',
            'output_directory_path',
            'separation_key',
            'attribute_group_column_name',
        ];

        foreach ($keys as $key) {
            if (! array_key_exists($key, $readSpreadSheet)) {
                throw new LogicException('There is no required setting value:'.$key);
            }
        }
    }
}
