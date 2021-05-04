<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Console\DefinitionDocumentCommand;

/**
 * Class DefinitionDocumentCommandServiceProvider.
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
     * Initial startup of all application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadViewsFrom(__DIR__.'/../../../resources/DefinitionDocument', 'spread_sheet_converter');
            $this->publishes([
                __DIR__.'/../../../resources/DefinitionDocument' => $this->app->resourcePath('views/vendor/spread_sheet_converter'),
            ], 'spread_sheet_converter');

            $this->publishes([
                __DIR__.'/../Config/step_up_dream/spread_sheet_converter.php' => config_path('step_up_dream/spread_sheet_converter.php'),
            ]);
        }
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->mergeConfigFrom(__DIR__.'/../Config/step_up_dream/spread_sheet_converter.php', 'step_up_dream.spread_sheet_converter');

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
