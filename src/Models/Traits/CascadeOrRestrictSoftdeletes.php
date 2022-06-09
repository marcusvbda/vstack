<?php

namespace marcusvbda\vstack\Models\Traits;

trait CascadeOrRestrictSoftdeletes
{
	protected static function bootCascadeOrRestrictSoftdeletes()
	{
		static::deleting(function ($model) {
			$model->validateRestrictSoftdeletes($model);
			$model->validateCascadeSoftdeletes($model);
		});
	}

	private function validateCascadeSoftdeletes($model)
	{
		$relations = $model->cascadeDeletes;
		foreach ($relations as $relation) {
			$model->{$relation}()->delete();
		}
	}

	private function validateRestrictSoftdeletes($model)
	{
		$relations = $model->restrictDeletes;
		foreach ($relations as $key => $relation) {
			$isCompound = !is_integer($key);
			if ($model->{($isCompound) ? $key : $relation}()->exists()) {
				abort(500, ($isCompound) ? $relation : "Não pode ser excluído pois está em uso");
			}
		}
	}
}
