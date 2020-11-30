<?php

namespace marcusvbda\vstack;

use Illuminate\Support\ServiceProvider;
use marcusvbda\vstack\Commands\{createResource, createFilter, createAction};
use marcusvbda\vstack\Middleware\HashIds;
use Illuminate\Routing\Router;

class vStackServiceProvider extends ServiceProvider
{
	public function boot(Router $router)
	{
		$this->loadRoutesFrom(__DIR__ . '/Routes/routes.php');
		$this->loadViewsFrom(__DIR__ . '/Views', 'vStack');
		$this->commands([
			createResource::class,
			createFilter::class,
			createAction::class,
		]);
		$this->publishes([
			__DIR__ . '/config' => config_path(),
			__DIR__ . '/migrations' => database_path("/migrations"),
		]);
		$router->aliasMiddleware('hashids',  HashIds::class);
	}
}