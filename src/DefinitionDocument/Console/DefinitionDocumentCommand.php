<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Console;

use LogicException;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\MultiGroup;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\Other;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\SingleGroup;

/**
 * Class DefinitionDocumentCommand
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Console
 */
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
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(): void
    {
        $targetCategory = $this->option('category');
        $targetFileName = $this->option('file_name');
        $readSpreadSheets = config('step_up_dream.spread_sheet_converter.read_spread_sheets');
        
        foreach ($readSpreadSheets as $readSpreadSheet) {
            if (!empty($targetCategory) && $targetCategory !== $readSpreadSheet['category_name']) {
                continue;
            }
            
            switch ($readSpreadSheet['read_type']) {
                case 'SingleGroup':
                    $creator = app()->make(SingleGroup::class, ['readSpreadSheet' => $readSpreadSheet]);
                    break;
                case 'MultiGroup':
                    $creator = app()->make(MultiGroup::class, ['readSpreadSheet' => $readSpreadSheet]);
                    break;
                case 'Other':
                    $creator = app()->make(Other::class, ['readSpreadSheet' => $readSpreadSheet]);
                    break;
                default:
                    throw new LogicException(sprintf('Unexpected value: %s', $readSpreadSheet['read_type']));
            }
            
            $creator->run($targetFileName);
            $this->info('Completed: '.$readSpreadSheet['category_name']);
        }
    }
}
