<?php

namespace marcusvbda\vstack\Models\Traits;

use Str;

trait HasSlug
{
    protected static function bootHasSlug()
    {
        static::saving(function ($model) {
            $sluggable = $model::sluggable();
            if ($sluggable) {
                $slug = Str::slug($model->{$sluggable["source"]});
                $model->attributes[$sluggable["raw"]] = $slug;
                $count = static::where($sluggable["raw"], $slug)->where('id', '!=', @$model->id)->count();
                if ($count > 0) {
                    $slug = $slug . "-" . $count;
                }
                $model->attributes[$sluggable["destination"]] = $slug;
            }
        });
    }
}
