<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Console;

use LogicException;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creator\Http;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creator\Table;

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
     * run command
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle() : void
    {
        $target_category = $this->option('category');
        $target_file_name = $this->option('file_name');
        
        $read_spread_sheets = config('spread_sheet.read_spread_sheets');
        
        foreach ($read_spread_sheets as $read_spread_sheet) {

            if (!empty($target_category) && $target_category !== $read_spread_sheet['category_name']) {
                continue;
            }
            
            switch ($read_spread_sheet['read_type']) {
                case 'Table':
                    $creator = app()->make(Table::class);
                    break;
                case 'Http':
                    $creator = app()->make(Http::class);
                    break;
                default:
                    throw new LogicException('Unexpected value');
            }
            
            $creator->run($read_spread_sheet['category_name'], $read_spread_sheet['use_blade'], $read_spread_sheet['sheet_id'], $read_spread_sheet['output_directory_path'], $target_file_name);
            $this->info('Completed: ' . $read_spread_sheet['category_name']);
        }
    }
}
