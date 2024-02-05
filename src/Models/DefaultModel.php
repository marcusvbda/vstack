<?php

namespace marcusvbda\vstack\Models;

use Illuminate\Database\Eloquent\Model;
use marcusvbda\vstack\Models\Traits\hasCode;
use marcusvbda\vstack\Models\Traits\hasTags;
use Illuminate\Database\Eloquent\SoftDeletes;
use marcusvbda\vstack\Models\Traits\CascadeOrRestrictSoftdeletes;
use marcusvbda\vstack\Models\Scopes\TenantScope;
use marcusvbda\vstack\Models\Observers\TenantObserver;
use marcusvbda\vstack\Models\Scopes\OrderByScope;
use marcusvbda\vstack\Models\Traits\HasSlug;
use marcusvbda\vstack\Models\Traits\useTenantTz;
use marcusvbda\vstack\Vstack;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class DefaultModel extends Model implements AuditableContract
{

	use hasCode, SoftDeletes, CascadeOrRestrictSoftdeletes, useTenantTz, hasTags, HasSlug, Auditable;
	public $guarded = ["created_at"];
	public $cascadeDeletes = [];
	public $restrictDeletes = [];
	public static $withoutAppends = false;

	public static function boot()
	{
		parent::boot();
		if (static::hasTenant()) {
			static::observe(new TenantObserver());
			static::addGlobalScope(new TenantScope());
			static::addGlobalScope(new OrderByScope(with(new static)->getTable()));
		}

		if (static::isAuditable()) {
			parent::enableAuditing();
		} else {
			parent::disableAuditing();
		}
	}

	public function scopeWithoutAppends($query)
	{
		self::$withoutAppends = true;

		return $query;
	}

	protected function getArrayableAppends()
	{
		if (self::$withoutAppends) {
			return [];
		}

		return parent::getArrayableAppends();
	}

	public function sluggable(): array
	{
		return [];
	}

	public static function hasTenant()
	{
		return true;
	}

	public static function isAuditable()
	{
		return false;
	}

	public function getFCreatedAtBadgeAttribute()
	{
		return Vstack::makeLinesHtmlAppend($this->f_created_at, $this->created_at->diffForHumans());
	}

	public function getFUpdatedAtBadgeAttribute()
	{
		return Vstack::makeLinesHtmlAppend($this->f_updated_at, $this->updated_at->diffForHumans());
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

	public function getAllowedFilters()
	{
		return [];
	}

	public function getAllowedIncludes()
	{
		return [];
	}

	public function getAllowedSorts()
	{
		return [];
	}
}
