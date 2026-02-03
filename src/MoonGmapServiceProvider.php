<?php

namespace Rosolovsky\MoonshineGmapField;

use Illuminate\Support\ServiceProvider;

class MoonGmapServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'gmap');
        $this->publishes([ __DIR__ . '/../resources/views' => resource_path('views/vendor/moonshine-gmap')], 'moonshine-gmap-views');
        $this->publishes([__DIR__ . '/../config/moonshine-gmap.php' => config_path('moonshine-gmap.php')], 'moonshine-gmap-config');

    }
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/moonshine-gmap.php', 'moonshine-gmap');
    }

}