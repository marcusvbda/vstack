<?php

namespace marcusvbda\vstack;

use Illuminate\Http\Request;

class Action
{
	public $id = "action-id";
	public $title = "action title";
	public $message = "action message";
	public $run_btn = "execute";

	public function __construct()
	{
		$this->id = strtolower(str_replace("\\", "-", static::class));
	}

	public function inputs()
	{
		return [];
	}

	public function handler(Request $request)
	{
		return ['success' => false, 'data' => $request->all(), "message" => "message here ..."];
	}
}
