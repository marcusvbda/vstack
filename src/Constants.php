<?php

namespace marcusvbda\vstack;

class Constants
{
	public static function options()
	{
		$constants = self::getAllContants();
		$result = array_map(function ($row) {
			return $row;
		}, $constants);
		return $result;
	}

	public static function getValue($index)
	{
		$constants = static::getAllContants();
		return $constants[$index];
	}

	public static function getIndex($label)
	{
		$constants = static::getAllContants();
		$status = array_filter($constants, function ($row) use ($label) {
			return $row === $label;
		});
		return array_key_first($status);
	}

	public static function getAllContants()
	{
		$class = new \ReflectionClass(static::class);
		return $class->getConstants();
	}
}