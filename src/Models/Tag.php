<?php

namespace marcusvbda\vstack\Models;

use Illuminate\Database\Eloquent\Model;
use marcusvbda\vstack\Models\Scopes\TenantScope;
use marcusvbda\vstack\Models\Observers\TenantObserver;
use marcusvbda\vstack\Traits\CascadeOrRestrictSoftdeletes;

class Tag extends Model
{
    use CascadeOrRestrictSoftdeletes;
    public $table = "resource_tags";
    public $guarded = ["created_at"];
    public $cascadeDeletes = ['relation'];
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

    public function relation()
    {
        return $this->hasMany(TagRelation::class, 'resource_tag_id');
    }
}
