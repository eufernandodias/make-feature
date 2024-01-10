<?php

namespace Eufernandodias\MakeFeature;

use Illuminate\Support\ServiceProvider;
use Eufernandodias\MakeFeature\Console\Commands\MakeFeatureCommand;

class MakeFeatureServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeFeatureCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/stubs/features/controllers' => base_path('stubs/eufernandodias/make-feature'),
            ], 'make-feature-stubs');
        }
    }
}
