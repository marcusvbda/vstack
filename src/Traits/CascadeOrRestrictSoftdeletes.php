<?php

namespace marcusvbda\vstack\Traits;

trait CascadeOrRestrictSoftdeletes
{
    protected static function bootCascadeOrRestrictSoftdeletes()
    {
        static::deleting(function ($model) {
            $model->validateCascadeSoftdeletes($model);
            $model->validateRestrictSoftdeletes($model);
        });
    }

    private function validateCascadeSoftdeletes($model)
    {
        $relations = $model->cascadeDeletes;
        foreach ($relations as $relation) $model->{$relation}()->delete();
    }

    private function validateRestrictSoftdeletes($model)
    {
        $relations = $model->restrictDeletes;
        foreach ($relations as $relation) {
            if ($model->{$relation}->count() >= 1) abort(500, "Não pode ser excluido pois está em uso");
        }
    }
}
