<?php

namespace marcusvbda\vstack;

use Carbon\Carbon;

class Vstack
{
	public static function current_version()
	{
		$content = file_get_contents(__DIR__ . "/../composer.json");
		$content = json_decode($content, true);
		return @$content["version"];
	}

	public static function theme()
	{
		return config("vstack.default_theme", "dark");
	}

	public static function last_version()
	{
		return "Verifique a versão mais atual em https://raw.githubusercontent.com/marcusvbda/vstack/master/composer.json";
	}

	public static function resource_field_route()
	{
		return route('resource.fielddata', ['resource' => "%%resource%%"]);
	}

	public static function default_upload_route()
	{
		return config("vstack.default_upload_route", "/admin/upload_file");
	}

	public static function default_import_csv_separator()
	{
		return config("vstack.default_import_csv_separator", ",");
	}

	public static function queue_resource_import()
	{
		return config("vstack.queue.resource-import", "resource-import");
	}

	public static function queue_resource_export()
	{
		return config("vstack.queue.resource-export", "resource-export");
	}

	public static function animation_enabled()
	{
		return config("vstack.animation.enabled", true);
	}

	public static function numberToInt($value)
	{
		return (int)preg_replace("/[^A-Za-z0-9]/", "", number_format($value, 2));
	}

	public static function intToNumber($value)
	{
		return (int)$value / 100;
	}

	public static function makeLinesHtmlAppend(...$args)
	{
		$html = "<div class='flex flex-col'>";
		foreach ($args as $key => $value) {
			if ($key == 0) {
				$html .= "<span><b class='dark:text-neutral-100'>{$value}</b></span>";
			} else {
				$html .= "<small class='text-neutral-400 dark:text-neutral-400 text-xs'>{$value}</small>";
			}
		}
		return $html . "</div>";
	}

	public static function toRawSql($query)
	{
		$bindings = $query->getBindings();
		$str = array_reduce($bindings, function ($sql, $binding) {
			return preg_replace('/\?/', is_numeric($binding) ? $binding : "'" . $binding . "'", $sql, 1);
		}, $query->toSql());
		return $str;
	}

	public static function getPageType($page_type)
	{
		$types = [
			"Clonagem" => "clone",
			"Cadastro" => "create",
			"Edição" => "edit",
			"Visualização" => "view",
		];
		return $types[$page_type] ?? "";
	}

	public static function orderQueryByAppend($query, $type, $index, $key = "id")
	{
		$sortedIds = $query->get()->{$type == "desc" ? 'sortByDesc' : 'sortBy'}($index)
			->pluck($key)
			->implode(",");
		return $query->orderByRaw("FIELD($key, $sortedIds)");
	}

	public static function niceBytes($val)
	{
		$numbers = 0;
		try {
			$val = trim($val);
			$numbers = (float)preg_replace("/[^0-9]/", "", $val);
			$unit = strtolower(preg_replace("/[0-9]/", "", $val));

			if (in_array($unit, ["g", 'gb'])) {
				$numbers *= (1024 * 1024 * 1024);
			}
			if (in_array($unit, ["m", 'mb'])) {
				$numbers *= (1024 * 1024);
			}
			if (in_array($unit, ["k", 'kb'])) {
				$numbers *= 1024;
			}
		} catch (\Exception $e) {
			return 0;
		}

		return $numbers;
	}

	public static function encodeJWT($data, $expiration = null)
	{
		$expiration = $expiration ? $expiration : Carbon::now()->add(config("vstack.api.token_expiration", "1 day"))->toDateTimeString();
		$header = json_encode(['typ' => 'JWT', 'alg' => 'HS256', 'expiration' => $expiration]);
		$payload = json_encode($data);
		$base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
		$base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
		$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, config("app.key"), true);
		$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
		$jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
		return [
			"token" => $jwt,
			"expiration_date" => $expiration,
		];
	}

	public static function decodeJWT($token)
	{
		$splited = explode(".", $token);
		$header = data_get($splited, '0', '');
		$header = str_replace(['-', '_'], ['+', '/'], $header) . "=";
		$expiration_str = data_get(json_decode(base64_decode($header)), 'expiration', '0');
		$signature = data_get($splited, '1', '');
		$signature = str_replace(['-', '_'], ['+', '/'], $signature) . "=";
		$result = json_decode(base64_decode($signature));
		if (Carbon::parse($expiration_str)->lte(Carbon::now())) {
			return false;
		}
		$test = data_get(static::encodeJWT($result, $expiration_str), 'token');
		return $test === $token ? $result : false;
	}

	public static function enumToOptions($cases, $filter = false)
	{
		if ($filter) {
			return collect($cases)->map(function ($x) {
				return ["value" => $x->name, "label" => $x->value];
			})->toArray();
		}
		return collect($cases)->map(function ($x) {
			return ["id" => $x->name, "value" => $x->value];
		})->toArray();
	}
}
