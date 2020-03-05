<?php

namespace marcusvbda\vstack\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\hasCode;
use Illuminate\Database\Eloquent\SoftDeletes;
use marcusvbda\vstack\Traits\CascadeOrRestrictSoftdeletes;
use marcusvbda\vstack\Models\Scopes\TenantScope;
use marcusvbda\vstack\Models\Observers\TenantObserver;

class DefaultModel extends Model
{
    use hasCode, SoftDeletes, CascadeOrRestrictSoftdeletes;
    public $guarded = ["created_at"];
    public $cascadeDeletes = [];
    public $restrictDeletes = [];
    public $relations = [];

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
