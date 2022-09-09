<?php

namespace marcusvbda\vstack\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceConfig extends Model
{
	protected $table = "resource_configs";
	public  $timestamps = false;
	public $guarded = ["created_at"];
	public $casts = [
		"data" => "object"
	];
}