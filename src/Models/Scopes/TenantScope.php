<?php

namespace marcusvbda\vstack\Models\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Auth;

class TenantScope implements Scope
{
	public function apply(Builder $builder, Model $model)
	{
		if (Auth::check()) {
			$user =  Auth::user();
			if($user->tenant_id) {
				@$builder->where($model->getTable() . '.' . 'tenant_id', $user->tenant_id);
			}
		}
	}
}