<?php

namespace marcusvbda\vstack\Models\Observers;

use App\User;
use Auth;

class TenantObserver
{
	public function creating($model)
	{
		if (Auth::check()) {
			if (!$model->tenant_id) {
				$model->tenant_id = Auth::user()->tenant_id;
			}
		}
	}
}