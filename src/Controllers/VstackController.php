<?php

namespace marcusvbda\vstack\Controllers;

use App\Http\Controllers\Controller;
use ResourcesHelpers;
use Illuminate\Http\Request;

class VstackController extends Controller
{
	public function getPartialContent($resource, Request $request)
	{
		$resource = ResourcesHelpers::find($resource);
		return  $this->{$request["type"]}($resource, $request);
	}

	protected function resourceTableContent($resource, $request)
	{
		$row = $resource->model->findOrFail($request["row_id"]);
		$content = [];
		foreach ($resource->table() as $key => $value) {
			if (strpos($key, "->") === false) {
				$content[$key] = @$row->{$key} ? $row->{$key} : " - ";
			} else {
				$value = $row;
				$_runner = explode("->", $key);
				foreach ($_runner as $idx) $value = @$value->{$idx};
				$content[$key] = ($value ? $value : ' - ');
			}
		}
		return $content;
	}

	public function getColumnIndex($columns, $row, $key, $placeholder = "          -          ")
	{
		$removeEmoji = function ($text) {
			$clean_text = "";
			$regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
			$clean_text = preg_replace($regexEmoticons, '', $text);
			$regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
			$clean_text = preg_replace($regexSymbols, '', $clean_text);
			$regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
			$clean_text = preg_replace($regexTransport, '', $clean_text);
			$regexMisc = '/[\x{2600}-\x{26FF}]/u';
			$clean_text = preg_replace($regexMisc, '', $clean_text);
			$regexDingbats = '/[\x{2700}-\x{27BF}]/u';
			$clean_text = preg_replace($regexDingbats, '', $clean_text);
			return $clean_text;
		};
		$value = "";
		if (!@$columns[$key]["handler"]) {
			$value = $row;
			$_runner = explode("->", $key);
			foreach ($_runner as $idx) $value = @$value->{$idx};
			$value = $removeEmoji(@trim($value) ? $value : $placeholder);
		} else  $value =  $removeEmoji(@trim(@$columns[$key]["handler"] ? $columns[$key]["handler"]($row) : $placeholder));
		return (@$value ? $value : $placeholder);
	}


	public function getJson(Request $request)
	{
		$model = @$request["model"];
		if (!$model) abort(403);
		$per_page = @$request["per_page"];
		$includes = @$request["includes"] ?? [];
		$fields = @$request["fields"] ?? ["*"];
		$order_by = @$request["order_by"] ?? ["id", "asc"];
		$query = app()->make($model)->where("id", ">", 0);
		$filters  = @$request["filters"] ?? [];
		$query = $this->processApiFilters($filters, $query);
		$result = $query->select($fields)->with($includes)
			->orderBy($order_by[0], $order_by[1]);
		return $per_page ? $result->paginate($per_page) : $result->get();
	}

	private function processApiFilters($filters, $query)
	{
		foreach ($filters as $filter_type => $filters) {
			foreach ($filters as $field => $queries) {
				if ($filter_type == "where") {
					$query = $query->where(function ($q) use ($field, $queries) {
						foreach ($queries as $key => $value) {
							$q = $q->where($field, $key, $value);
						}
					});
				}
				if ($filter_type == "or_where") {
					$query = $query->where(function ($q) use ($field, $queries) {
						foreach ($queries as $key => $value) {
							$q = $q->orWhere($field, $key, $value);
						}
					});
				}
				if ($filter_type == "or_where_in") {
					$query = $query->where(function ($q) use ($field, $queries) {
						foreach ($queries as $key => $value) {
							$q = $q->orWhereIn($field, $value);
						}
					});
				}
				if ($filter_type == "where_in") {
					$query = $query->where(function ($q) use ($field, $queries) {
						foreach ($queries as $key => $value) {
							$q = $q->whereIn($field, $value);
						}
					});
				}

				if ($filter_type == "or_where_not_in") {
					$query = $query->where(function ($q) use ($field, $queries) {
						foreach ($queries as $key => $value) {
							$q = $q->OrWhereNotIn($field, $value);
						}
					});
				}

				if ($filter_type == "where_not_in") {
					$query = $query->where(function ($q) use ($field, $queries) {
						foreach ($queries as $key => $value) {
							$q = $q->whereNotIn($field, $value);
						}
					});
				}
			}
		}
		return $query;
	}
}
