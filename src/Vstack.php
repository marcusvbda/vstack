<?php

namespace marcusvbda\vstack;

class Vstack
{
	public static function current_version()
	{
		$content = file_get_contents(__DIR__ . "/../composer.json");
		$content = json_decode($content, true);
		return @$content["version"];
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

	public static function resource_export_extension()
	{
		return config("vstack.resource_export_extension", "xls");
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

	public static function makeTwoLinesHtmlAppend($line, $subline)
	{
		return "
            <div class='d-flex flex-column'>
                <span><b>{$line}</b></span>
                <small class='text-muted'>{$subline}</small>
            </div>
        ";
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
		if ($page_type == "Clonage") return "clone";
		if ($page_type == "Cadastro") return "create";
		if ($page_type == "Edição") return "edit";
		if ($page_type == "Visualização") return "view";
		return "";
	}
}