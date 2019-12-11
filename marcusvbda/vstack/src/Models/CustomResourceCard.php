<?php
namespace marcusvbda\vstack\Models;
use marcusvbda\vstack\Models\DefaultTenantModel;
use marcusvbda\vstack\Models\Scopes\UserScope;
use marcusvbda\vstack\Models\Observers\UserObserver;

class CustomResourceCard extends DefaultTenantModel
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
}