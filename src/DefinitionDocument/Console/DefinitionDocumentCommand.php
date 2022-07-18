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
        $targetCategory = $this->option('category');
        $targetFileName = $this->option('file_name');
        $readSpreadSheets = config('step_up_dream.spread_sheet_converter.read_spread_sheets');

        foreach ($readSpreadSheets as $readSpreadSheet) {
            if (! empty($targetCategory) && $targetCategory !== $readSpreadSheet['category_name']) {
                continue;
            }

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
}
