<?php

namespace marcusvbda\vstack\Models;

use Illuminate\Database\Eloquent\Model;
use marcusvbda\vstack\Models\Traits\hasCode;
use Illuminate\Database\Eloquent\SoftDeletes;
use marcusvbda\vstack\Traits\CascadeOrRestrictSoftdeletes;
use marcusvbda\vstack\Models\Scopes\TenantScope;
use marcusvbda\vstack\Models\Observers\TenantObserver;
use marcusvbda\vstack\Models\Traits\useTenantTz;

class DefaultModel extends Model
{
	use hasCode, SoftDeletes, CascadeOrRestrictSoftdeletes, useTenantTz;
	public $guarded = ["created_at"];
	public $cascadeDeletes = [];
	public $restrictDeletes = [];
	public static function boot()
	{
		parent::boot();
		if (static::hasTenant()) {
			static::observe(new TenantObserver());
			static::addGlobalScope(new TenantScope());
		}
	}

	public static function hasTenant()
	{
		return true;
	}
}