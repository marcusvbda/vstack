<?php

namespace marcusvbda\vstack\Mutators;

class SetUserData extends BaseMutator
{
	protected $needsAuth = true;
	public function process($content)
	{
		$user = $this->user;
		$content["user"] = [
			"id"    => @$user->id ? @$user->id : null,
			"code"  => @$user->code ? @$user->code : null,
			"name"  => @$user->name ? @$user->name : null,
			"email" => @$user->email ? @$user->email : null,
		];

		return $content;
	}
}