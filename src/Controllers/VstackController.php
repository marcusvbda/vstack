<?php

namespace marcusvbda\vstack\Controllers;

use App\Http\Controllers\Controller;
use ResourcesHelpers;
use Illuminate\Http\Request;
use marcusvbda\vstack\Components;
use Spatie\QueryBuilder\QueryBuilder;

class VstackController extends Controller
{
	public function minifyHtml($html)
	{
		$search = [
			'/(\n|^)(\x20+|\t)/',
			'/(\n|^)\/\/(.*?)(\n|$)/',
			'/\n/',
			'/\<\!--.*?-->/',
			'/(\x20+|\t)/', # Delete multispace (Without \n)
			'/\>\s+\</', # strip whitespaces between tags
			'/(\"|\')\s+\>/', # strip whitespaces between quotation ("') and end tags
			'/=\s+(\"|\')/',
		]; # strip whitespaces between = "'

		$replace = [
			"\n",
			"\n",
			" ",
			"",
			" ",
			"><",
			"$1>",
			"=$1",
		];

		$html = preg_replace($search, $replace, $html);
		return $html;
	}

	public function LoadComponent(Request $request)
	{
		$comp = (new Components($request->callback, $request->payload));
		$content =  $comp->render();
		return response()->json(["content" => $this->minifyHtml($content)]);
	}

	public function getPartialContent($resource, Request $request)
	{
		$resource = ResourcesHelpers::find($resource);
		return  $this->{$request["type"]}($resource, $request);
	}

	public function resourceTableContent($resource, $request, $row = null, $force_id = false, $include_after_row = false)
	{
		if (@!$row) {
			$row = $resource->useRawContentOnList() ? (object)$request['raw_content'] : $resource->model->findOrFail($request["row_id"]);
		}
		$content = [];
		if (@$request["complete_model"]) {
			$content = $row;
		} else {
			if ($force_id) {
				$content["id"] = $row->id;
				$content["code"] = $row->code;
			}
			if ($include_after_row) {
				$content["after_row"] = $resource->tableAfterRow($row);
			}
			if ($resource->useTags()) {
				$content["tags"] = $row->tags;
			}
			foreach ($resource->table() as $key => $value) {
				$content[$key] = $resource->tableRowContent($row, $key, $value);
			}
		}
		$acl = [
			"can_view_audits" => $resource->checkAclResource($row, "viewAudits"),
			"can_update" => $resource->checkAclResource($row, "update"),
			"can_clone" => $resource->checkAclResource($row, "clone"),
			"can_delete" => $resource->checkAclResource($row, "delete"),
			"can_view" => $resource->checkAclResource($row, "view"),
			"use_tags" => $resource->useTags(),
			"resource_singular_label" => $resource->singularLabel(),
			"resource_label" => $resource->label(),
			"resource_icon" => $resource->icon(),
			"before_delete" => array_map(function ($row) {
				unset($row["handler"]);
				return $row;
			}, @$resource->beforeDelete() ?? [])
		];
		return ["row_class" => $resource->tableRowClass($row), "content" => $content, "acl" => $acl, "additional_extra_buttons" => $resource->extraActionButtons($row)];
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
		$handler = data_get($columns, $key . ".handler");
		if ($handler) {
			$value = $removeEmoji(trim($handler($row)));
		}
		if (@trim($value) === "") {
			$value = null;
		}
		return (@$value !== null ? $value : $placeholder);
	}

	public function getJson(Request $request)
	{
		if ($request->isMethod('post') && ($request->params || $request->json)) {
			$request = new Request(@$request->params ? $request->params : $request->json);
		}

		if (@$request->filter_class_name) {
			$_class = app()->make($request->filter_class_name, ['model' => @$request->model ?? null]);
			if (method_exists($_class, 'fetchOptionsHandler')) {
				return $_class->fetchOptionsHandler($request);
			}
		}

		$model = @$request["model"];
		if (!$model) {
			abort(400);
		}

		$per_page = @$request["per_page"];
		$includes = @$request["includes"] ?? [];
		$fields = @$request["fields"] ?? ["*"];
		$order_by = @$request["order_by"];
		$query = app()->make($model);
		$filters  = @$request["filters"] ?? [];

		if (is_string($filters)) {
			$filters = json_decode($filters, true);
		}

		$query = $this->processApiFilters($filters, $query);
		$result = $query->select($fields)->with($includes);
		if ($order_by) {
			$query = $query->orderBy($order_by[0], $order_by[1]);
		}
		return $per_page ? $result->paginate($per_page) : $result->get();
	}

	private function processApiFilters($filters, $query)
	{
		foreach ($filters as $filter_type => $filter) {
			if ($filter_type == "where") $query = $query->where($filter);
			if ($filter_type == "or_where") {
				$query = $query->where(function ($q) use ($filter) {
					foreach ($filter as $f) {
						$q->orWhere([$f]);
					}
				});
			}
			if ($filter_type == "or_where_in") {
				$query = $query->where(function ($q) use ($filter) {
					foreach ($filter as $f) {
						$q->orWhereIn([$filter]);
					}
				});
			}
			if ($filter_type == "where_in") {
				$query = $query->where(function ($q) use ($filter) {
					foreach ($filter as $f) {
						$q->whereIn($f[0], $f[1]);
					}
				});
			}
			if ($filter_type == "or_where_not_in") {
				$query = $query->where(function ($q) use ($filter) {
					foreach ($filter as $f) {
						$q->orWhereNotIn($f[0], $f[1]);
					}
				});
			}
			if ($filter_type == "raw_where") {
				$query = $query->where(function ($q) use ($filter) {
					foreach ($filter as $f) {
						$q->whereRaw($f);
					}
				});
			}
		}
		return $query;
	}

	public function grapesEditor()
	{
		return view("vStack::resources.field.grapes.iframe");
	}

	public function queryBuilder($model)
	{
		$model = app()->make("\\" . str_replace("-", "\\", $model));
		return QueryBuilder::for($model)
			->allowedFilters($model->getAllowedFilters())
			->allowedSorts($model->getAllowedSorts())
			->allowedIncludes($model->getAllowedIncludes())
			->get();
	}
}
