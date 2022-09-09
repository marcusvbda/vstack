<?php

namespace marcusvbda\vstack\Middleware;

use Illuminate\Routing\Pipeline;
use Closure;
use marcusvbda\vstack\Mutators\{
	SetChatData,
	SetUserData,
	SetGeneralMutator,
	SetVstackMutator,
	SetTenantData,
	ConfigData,
};

class SendJSVarsToView
{
	private $variableNameInBlade = "javascript_globals";

	private $pipes = [
		SetUserData::class,
		SetChatData::class,
		SetGeneralMutator::class,
		SetVstackMutator::class,
		SetTenantData::class,
		ConfigData::class
	];

	private $globals = [];

	public function handle($request, Closure $next)
	{
		$pipes = array_merge($this->pipes, config("vstack.extra_javascript_global_variables") ? config("vstack.extra_javascript_global_variables") : []);
		return (app(Pipeline::class))
			->send($this->globals)
			->through($pipes)
			->then(function ($v) use ($request, $next) {
				view()->share($this->variableNameInBlade, json_encode($v));
				return $next($request);
			});
	}
}