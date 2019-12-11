<?php

namespace marcusvbda\vstack\Models\Observers;
use App\User;
use Auth;

class UserObserver
{
    public function creating($model)
    {
        if (Auth::check()) {
            if (!$model->user_id) {
                $model->user_id = Auth::user()->id;
            }
        }
    }
}
