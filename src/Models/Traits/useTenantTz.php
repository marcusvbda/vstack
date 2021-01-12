<?php

namespace marcusvbda\vstack\Models\Traits;

use Carbon\Carbon;

trait useTenantTz
{
	protected function asDateTime($value): Carbon
	{
		if (is_numeric($value)) return parent::asDateTime($value);
		$justCreated = false;

		if (!($value instanceof \DateTime)) {
			$value = new \DateTime($value);
			$justCreated = true;
		}
		$tz = config('app.timezone');
		if ($value->getTimezone()->getName() !== $tz) {
			if (!$justCreated) {
				$value = clone $value;
			}
			$value->setTimezone(new \DateTimeZone($tz));
		}
		return parent::asDateTime($value);
	}
}