<?php

/** @noinspection PhpUndefinedMethodInspection */

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
     * Processing after service initial processing registration
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../../../resources/DefinitionDocument', 'definition_document');
    
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../../resources/DefinitionDocument' => $this->app->resourcePath('views/vendor/definition_document'),
            ], 'definition_document');
        
            $this->commands(array_values($this->commands));
        }
    }
    
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/spread_sheet.php', 'spread_sheet');
    
        $this->app->singleton('command.create.definition.document', function ($app) {
            return $app->make(DefinitionDocumentCommand::class);
        });
    
    }
    
    /**
     * Get the services provided by the provider.
     */
    public function provides()
    {
        return array_values($this->commands);
    }
}
