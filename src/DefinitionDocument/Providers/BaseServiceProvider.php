<?php

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class BaseServiceProvider
 *
 * @package FutureDream\DreamAbility\SpreadSheet\Providers
 */
class BaseServiceProvider extends ServiceProvider
{
    /**
     * Processing after service initial processing registration
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../Config/spread_sheet.php' => config_path('spread_sheet.php'),
        ]);
    }
}
