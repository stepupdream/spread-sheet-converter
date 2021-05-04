<?php

namespace StepUpDream\SpreadSheetConverter\SpreadSheetReader\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use StepUpDream\SpreadSheetConverter\SpreadSheetReader\Readers\SpreadSheetReader;

/**
 * Class SpreadSheetReaderProvider.
 */
class SpreadSheetReaderProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->singleton(SpreadSheetReader::class, function ($app) {
            return new SpreadSheetReader();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            SpreadSheetReader::class,
        ];
    }
}
