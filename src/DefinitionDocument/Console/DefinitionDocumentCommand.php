<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Console;

use LogicException;
use StepUpDream\DreamAbilitySupport\Console\BaseCommand;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\CreatorFactory;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators\Struct\SpreadSheetConfig;

/**
 * Console command for creating definition documents.
 */
class DefinitionDocumentCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spread-sheet-converter:create-definition-document {--category=} {--file_name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create definition document {any:category} {any:file_name}.';

    /**
     * Run command.
     */
    public function handle(): void
    {
        $targetCategory = $this->optionText('category');
        $targetFileName = $this->optionText('file_name');
        $readSpreadSheetConfigs = $this->readSpreadSheetConfigs();

        foreach ($readSpreadSheetConfigs as $readSpreadSheetConfig) {
            if ($targetCategory !== $readSpreadSheetConfig->categoryTag()) {
                continue;
            }

            $creator = (new CreatorFactory($this->output))->make($readSpreadSheetConfig);
            $creator->run($targetFileName);
        }

        $this->commandDetailLog();
    }

    /**
     * Read Spread Sheets
     *
     * @return SpreadSheetConfig[] $spreadSheetConfigs
     */
    private function readSpreadSheetConfigs(): array
    {
        $readSpreadSheets = config('stepupdream.spread-sheet-converter.read_spread_sheets');

        if (!is_array($readSpreadSheets) || !$this->isMultidimensional($readSpreadSheets)) {
            throw new LogicException('Must be a two-dimensional array:read_spread_sheets');
        }

        $spreadSheetConfigs = [];
        foreach ($readSpreadSheets as $readSpreadSheet) {
            /** @var array<string, string> $readSpreadSheet */
            $spreadSheetConfigs[] = new SpreadSheetConfig($readSpreadSheet);
        }

        return $spreadSheetConfigs;
    }
}
