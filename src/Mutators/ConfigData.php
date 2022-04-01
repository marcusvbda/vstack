<?php

namespace marcusvbda\vstack\Mutators;

class ConfigData extends BaseMutator
{
	protected $needsAuth = false;
	public function process($content)
	{
		$app = config("app");
		$content["config"] = [
			"name"  => $app["name"],
			"env"  => $app["env"],
			"debug"  => $app["debug"],
			"timezone"  => $app["timezone"],
			"locale"  => $app["locale"]
		];

		return $content;
	}
}
