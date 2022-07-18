<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Console\DefinitionDocumentCommand;

/**
 * Class DefinitionDocumentCommandServiceProvider
 *
 * @package StepUpDream\SpreadSheetConverter\DefinitionDocument\Providers
 */
class DefinitionDocumentCommandServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        'CreateDefinitionDocument' => 'command.create.definition.document',
    ];
    
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->mergeConfigFrom(__DIR__.'/../Config/spread_sheet.php', 'step_up_dream.spread_sheet_converter');
            $this->loadViewsFrom(__DIR__.'/../../../resources/DefinitionDocument', 'spread_sheet_converter');
            
            $this->publishes([
                __DIR__.'/../Config/spread_sheet.php' => config_path('step_up_dream/spread_sheet_converter.php'),
            ]);
            $this->publishes([
                __DIR__.'/../../../resources/DefinitionDocument' => $this->app->resourcePath('views/vendor/spread_sheet_converter'),
            ], 'spread_sheet_converter');
            
            $this->app->singleton('command.create.definition.document', function ($app) {
                return new DefinitionDocumentCommand();
            });
            
            $this->commands(array_values($this->commands));
        }
    }
    
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return array_values($this->commands);
    }
}
