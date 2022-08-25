<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\SpreadSheetService\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use StepUpDream\SpreadSheetConverter\SpreadSheetService\GoogleService;
use StepUpDream\SpreadSheetConverter\SpreadSheetService\Readers\SpreadSheetReader;

class SpreadSheetProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->singleton(SpreadSheetReader::class, function () {
            $googleService = new GoogleService();

            return new SpreadSheetReader($googleService);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return mixed[]
     */
    public function provides(): array
    {
        return [
            SpreadSheetReader::class,
        ];
    }
}
