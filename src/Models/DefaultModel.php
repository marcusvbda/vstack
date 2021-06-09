<?php

namespace marcusvbda\vstack\Models;

use Illuminate\Database\Eloquent\Model;
use marcusvbda\vstack\Models\Traits\hasCode;
use Illuminate\Database\Eloquent\SoftDeletes;
use marcusvbda\vstack\Models\Traits\CascadeOrRestrictSoftdeletes;
use marcusvbda\vstack\Models\Scopes\TenantScope;
use marcusvbda\vstack\Models\Observers\TenantObserver;
use marcusvbda\vstack\Models\Traits\useTenantTz;
use marcusvbda\vstack\Vstack;

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

	public function makeTwoLinesHtmlAppend($line, $subline)
	{
		return Vstack::makeTwoLinesHtmlAppend($line, $subline);
	}

	public function getFCreatedAtBadgeAttribute()
	{
		return $this->makeTwoLinesHtmlAppend($this->f_created_at, $this->created_at->diffForHumans());
	}

	public function getFUpdatedAtBadgeAttribute()
	{
		return $this->makeTwoLinesHtmlAppend($this->f_updated_at, $this->updated_at->diffForHumans());
	}

	public function getFCreatedAtAttribute()
	{
		if (!@$this->created_at) return;
		return $this->created_at->format("d/m/Y - H:i:s");
	}

	public function getFUpdatedAtAttribute()
	{
		if (!@$this->updated_at) return;
		return $this->updated_at->format("d/m/Y - H:i:s");
	}
}
