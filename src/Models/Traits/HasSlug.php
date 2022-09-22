<?php

namespace marcusvbda\vstack\Models\Traits;

use Str;

trait HasSlug
{
    protected static function bootHasSlug()
    {
        static::saving(function ($model) {
            $sluggable = $model->sluggable();
            if ($sluggable && count($sluggable)) {

                foreach ($sluggable as $key => $value) {
                    $source = data_get($value, 'source', false);
                    if ($source) {
                        $slug = Str::slug($model->{$source});
                        $count = static::where($key, $slug)->where('id', '!=', @$model->id)->count();
                        if ($count > 0) {
                            $slug = $slug . "-" . $count;
                        }
                        $model->attributes[$key] = $slug;
                    }
                }
            }
        });
    }
}
