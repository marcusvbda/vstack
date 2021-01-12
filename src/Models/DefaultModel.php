<?php

namespace marcusvbda\vstack\Models;

use Illuminate\Database\Eloquent\Model;
use marcusvbda\vstack\Models\Traits\hasCode;
use Illuminate\Database\Eloquent\SoftDeletes;
use marcusvbda\vstack\Traits\CascadeOrRestrictSoftdeletes;
use marcusvbda\vstack\Models\Scopes\TenantScope;
use marcusvbda\vstack\Models\Observers\TenantObserver;
use Carbon\Carbon;
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

	public function getCreatedAtAttribute($value)
	{
		return $this->tenantTimezone($value);
	}

	protected function tenantTimezone($value)
	{
		$user = \Auth::user();
		$tz = config('app.timezone');
		if ($tenant = @$user->tenant) $tz =  @$tenant->timezone ?? config("app.timezone");
		$_tz = @config("timezones")[$tz] ?? "UTC";
		return Carbon::create($value)->tz($_tz);
	}

	public function getUpdatedAtAttribute($value)
	{
		return $this->tenantTimezone($value);
	}

	public function getDeletedAtAttribute($value)
	{
		$timezone = config("app.timezone");
		return Carbon::parse($value, "UTC")->tz($timezone);
	}

	public static function hasTenant()
	{
		return true;
	}
}