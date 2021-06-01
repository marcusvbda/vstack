<?php

namespace marcusvbda\vstack\Mutators;

use marcusvbda\vstack\Vstack;

class SetVstackMutator extends BaseMutator
{
	protected $needsAuth = false;
	public function process($content)
	{
		$content["vstack"] = [
			"version" => [
				"current" => Vstack::current_version(),
				"last" => Vstack::last_version(),
			],
			"resource_field_route"   => Vstack::resource_field_route(),
			"default_upload_route"   => Vstack::default_upload_route(),
			"default_import_csv_separator"   => Vstack::default_import_csv_separator(),
			"animation_enabled"   => Vstack::animation_enabled(),
		];
		return $content;
	}
}