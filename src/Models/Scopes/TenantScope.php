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
            @$builder->where($model->table.'.'.'tenant_id', Auth::user()->tenant_id);
        }
    }
}
