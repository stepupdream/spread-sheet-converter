<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Console\DefinitionDocumentCommand;

class DefinitionDocumentCommandServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Config path to output.
     *
     * @var string
     */
    protected string $originPathConfig = __DIR__.'/../Config/stepupdream/spread-sheet-converter.php';

    /**
     * The commands to be registered.
     *
     * @var string[]
     */
    protected array $commands = [
        'CreateDefinitionDocument' => 'command.create.definition.document',
    ];

    /**
     * Initial startup of all application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadViewsFrom(__DIR__.'/../../../resources/DefinitionDocument', 'spread-sheet-converter');

            $originPathDocument = __DIR__.'/../../../resources/DefinitionDocument';
            $targetPathDocument = 'views/vendor/spread-sheet-converter';
            $this->publishes([
                $originPathDocument => $this->app->resourcePath($targetPathDocument),
            ], 'spread-sheet-converter');

            $this->publishes([
                $this->originPathConfig => config_path('stepupdream/spread-sheet-converter.php'),
            ]);
        }
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->mergeConfigFrom($this->originPathConfig, 'stepupdream.spread-sheet-converter');

            $this->app->singleton('command.create.definition.document', function () {
                return new DefinitionDocumentCommand();
            });

            $this->commands(array_values($this->commands));
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return mixed[]
     */
    public function provides(): array
    {
        return array_values($this->commands);
    }
}
