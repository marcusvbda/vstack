<?php
namespace marcusvbda\vstack\Models;
use marcusvbda\vstack\Models\DefaultModel;
use marcusvbda\vstack\Models\Scopes\UserScope;
use marcusvbda\vstack\Models\Observers\UserObserver;

class CustomResourceCard extends DefaultModel
{
    protected $table = "custom_resource_cards";
    // public $cascadeDeletes = [];
    // public $restrictDeletes = [];

    public static function boot()
    {
        parent::boot();
        static::observe(new UserObserver());
        static::addGlobalScope(new UserScope());
    }

    public static function hasTenant()
    {
        return false;
    }
}