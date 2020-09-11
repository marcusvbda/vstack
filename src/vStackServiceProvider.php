<?php

namespace marcusvbda\vstack;

use Illuminate\Support\ServiceProvider;
use marcusvbda\vstack\Commands\{createResource, createFilter, createMetric};

class vStackServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/Routes/routes.php');
        $this->loadViewsFrom(__DIR__ . '/Views', 'vStack');
        $this->commands([
            createResource::class,
            createFilter::class,
        ]);
        $this->publishes([
            __DIR__ . '/config' => config_path(),
            __DIR__.'/migrations' => database_path("/migrations"),
        ]);
    }
}
