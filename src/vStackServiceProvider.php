<?php

namespace marcusvbda\vstack;

use Illuminate\Support\ServiceProvider;
use marcusvbda\vstack\Commands\{createFilter, createMetric, MetricMakeCommand, ResourceMakeCommand, ModelMakeCommand};

class vStackServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/Routes/routes.php');
        $this->loadViewsFrom(__DIR__ . '/Views', 'vStack');
        $this->commands([
            ModelMakeCommand::class,
            createFilter::class,
            createMetric::class,
            ResourceMakeCommand::class,
            MetricMakeCommand::class
        ]);
        $this->publishes([
            __DIR__ . '/config' => config_path(),
            __DIR__ . '/migrations' => database_path("/migrations"),
        ]);
    }
}
