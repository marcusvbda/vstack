<?php

namespace marcusvbda\vstack\Mutators;

class SetTenantData extends BaseMutator
{
	protected $needsAuth = true;
	public function process($content)
	{
		$user = $this->user;
		$tenant = @$user->tenant;
		$content["tenant"] = [
			"id"    => @$tenant->id ? @$tenant->id : null,
			"code"  => @$tenant->code ? @$tenant->code : null,
			"name"  => @$tenant->name ? @$tenant->name : null
		];

		return $content;
	}
}