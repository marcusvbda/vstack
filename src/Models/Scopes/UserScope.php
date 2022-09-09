<?php

namespace marcusvbda\vstack\Models\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Auth;

class UserScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (Auth::check()) {
            @$builder->where($model->getTable() . '.' . 'user_id', Auth::user()->id);
        }
    }
}
