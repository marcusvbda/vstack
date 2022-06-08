<?php

namespace marcusvbda\vstack;

use Illuminate\Support\ServiceProvider;
use marcusvbda\vstack\Commands\{asyncHandler, createResource, createFilter, createAction, clearResourceExport};
use marcusvbda\vstack\Middleware\HashIds;
use Illuminate\Routing\Router;
use marcusvbda\vstack\Middleware\{JwtAuth};

class vStackServiceProvider extends ServiceProvider
{
	public function boot(Router $router)
	{
		$this->loadRoutesFrom(__DIR__ . '/Routes/routes.php');
		$this->loadViewsFrom(__DIR__ . '/Views', 'vStack');
		$router->aliasMiddleware('api.vstack_jwt', JwtAuth::class);
		$this->commands([
			createResource::class,
			createFilter::class,
			createAction::class,
			clearResourceExport::class
		]);
		$this->publishes([
			__DIR__ . '/config' => config_path(),
			__DIR__ . '/migrations' => database_path("/migrations"),
		]);
		$router->aliasMiddleware('hashids',  HashIds::class);
	}
}
