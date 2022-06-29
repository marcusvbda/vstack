<?php

namespace marcusvbda\vstack\Controllers;

use App\Http\Controllers\Controller;
use ResourcesHelpers;
use Illuminate\Http\Request;
use Storage;
use Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use marcusvbda\vstack\Exports\DefaultGlobalExporter;
use Maatwebsite\Excel\HeadingRowImport;
use Excel;
use Illuminate\Http\Exceptions\HttpResponseException;
use marcusvbda\vstack\Models\{Tag, TagRelation};
use marcusvbda\vstack\Models\ResourceConfig;
use marcusvbda\vstack\Vstack;
use Validator;


class ResourceController extends Controller
{
	public function report($resource, Request $request)
	{
		request()->request->add(["page_type" => "report"]);
		return $this->showIndexList($resource, $request, true);
	}

	public function getListData($resource, $type, Request $request)
	{
		$resource = ResourcesHelpers::find($resource);
		$report_mode = $type == "report";
		if ($report_mode) {
			request()->request->add(["page_type" => "report"]);
		} else {
			request()->request->add(["page_type" => "list"]);
		}
		if ($report_mode) {
			if (!$resource->canViewReport()) {
				abort(403);
			}
		} else {
			if (!$resource->canViewList()) {
				abort(403);
			}
		}
		$socket_client_id = @$request->socket_client_id;
		$data = $this->getData($resource, $request);
		$per_page = $this->getPerPage($resource);
		$list_items = $resource->listItemsContent(clone $data);
		$data = $data->select("*")->paginate($per_page);
		if ($report_mode) {
			$data->setPath(route('resource.report', ["resource" => $resource->id]));
		} else {
			$data->setPath(route('resource.index', ["resource" => $resource->id]));
		}
		if (@$request["list_type"]) {
			$this->storeListType($resource, $request["list_type"]);
		}
		$user = Auth::user();

		$model_count = $resource->model->count();
		if (!$model_count) {
			$template = "<div>" . view("vStack::resources.loader.no_data", compact("resource", "model_count"))->render() . "</div>";
			$minified_template = ResourcesHelpers::minify($template);
			$template_chunked = str_split($minified_template, 100);
			$response = ['template' => $template_chunked, "type" => "no_data"];
			if (!$socket_client_id) {
				return json_encode($response, JSON_INVALID_UTF8_IGNORE);
			} else {
				Vstack::SocketEmit('template', $socket_client_id, $response, "client");
				return ["success" => true, "message" => "response with socket"];
			}
		} else {
			$filters = $resource->filters();
			$_data =  request()->all();
			if (@$_data["page"]) {
				unset($_data['page']);
			}
			if (@$_data["page_type"]) {
				unset($_data['page_type']);
			}

			$topGetter = function () use ($filters, $_data, $resource, $data, $list_items, $report_mode, $user) {
				$template = "<div>" . view("vStack::resources.loader.data_top", compact("filters", "_data", "resource", "data", "list_items", "report_mode", "user"))->render() . "</div>";
				$template = ResourcesHelpers::minify($template);
				$template = str_split($template, 500);
				return $template;
			};

			$tableGetter = function () use ($filters, $_data, $resource, $data, $list_items, $report_mode, $user) {
				$template = "<div>" . view("vStack::resources.loader.data_table", compact("filters", "_data", "resource", "data", "list_items", "report_mode", "user"))->render() . "</div>";
				$template = ResourcesHelpers::minify($template);
				$template = str_split($template, 500);
				return $template;
			};

			$top = $topGetter();
			$table = $tableGetter();
			return json_encode(['top' => $top, "table" => $table, "type" => "data"],  JSON_INVALID_UTF8_IGNORE);
		}
	}

	protected function showIndexList($resource, Request $request, $report_mode = false)
	{
		$resource = ResourcesHelpers::find($resource);

		if ($report_mode) {
			if (!$resource->canViewReport()) {
				abort(403);
			}
		} else {
			if (!$resource->canViewList()) {
				abort(403);
			}
		}
		if (request()->is_api) {
			$data = $this->getData($resource, $request);
			$per_page = $this->getPerPage($resource);
			$data = $data->select("*")->paginate($per_page);
			return $data;
		}
		return view("vStack::resources.index", compact("resource", "report_mode"));
	}

	private function storeListType($resource, $type)
	{
		if (Auth::check()) {
			$user = Auth::user();
			$config = ResourceConfig::where("data->user_id", $user->id)->where("resource", $resource->id)->where("config", 'list_type')->first();
			$config = @$config->id ? $config : new ResourceConfig;
			$_data = @$config->data ?? (object)[];
			$_data->type = $type;
			$_data->user_id = $user->id;
			$config->data = $_data;
			$config->resource = $resource->id;
			$config->config = 'list_type';
			$config->save();
		}
	}

	public function createReportTemplate($resource, Request $request)
	{
		$resource = ResourcesHelpers::find($resource);
		if (!$resource->canViewReport()) abort(403);
		if (!$resource->canCreateReportTemplates()) abort(403);
		$user = Auth::user();
		$config = ResourceConfig::where("data->user_id", $user->id)->where("resource", $resource->id)->where("config", "report_templates")->first();
		$config = @$config->id ? $config : new ResourceConfig;

		$config->resource = $resource->id;
		$config->config = "report_templates";
		$_data = @$config->data ?? (object)[];
		$templates = @$_data->templates ?? [];
		$templates = $request->all();
		$_data->templates = $templates;
		$_data->user_id = $user->id;
		$config->data = $_data;
		$config->save();

		return ["success" => true, "reports" => $templates];
	}


	public function index($resource, Request $request)
	{
		request()->request->add(["page_type" => "list"]);
		return $this->showIndexList($resource, $request);
	}

	private function makeSorterHandler($resource, $query, $orderBy, $orderType)
	{
		$table = $resource->model->getTable();
		$tableFields = $resource->table();
		if (@$tableFields[$orderBy]["sortable_handler"]) {
			return $tableFields[$orderBy]["sortable_handler"]($query, $orderType);
		} else {
			$sortableIndex = @$tableFields[$orderBy]["sortable_index"];
			return $query->orderBy($table . "." . ($sortableIndex ? $sortableIndex : $orderBy), $orderType);
		}
	}

	public function getData($resource, Request $request, $query = null)
	{
		$table = $resource->model->getTable();

		if ($resource->isNoModelResource()) {
			return $resource->model->where("id", "<", 0);
		}

		$table = $resource->model->getTable() . ".";
		$data      = $request->all();
		$orderBy   = Arr::get($data, 'order_by', "id");
		$orderType = Arr::get($data, 'order_type', "desc");
		$query     = $query ? $query : $resource->model->select($table . "id")->where($table . "id", ">", 0);

		foreach ($resource->filters() as $filter) {
			$query = $filter->applyFilter($query, $data);
		}

		$search = $resource->search();

		if (@$data["_"]) {
			$query = $query->where(function ($q) use ($search, $data, $table) {
				foreach ($search as $s) {
					if (is_callable($s)) {
						$q = $s($q, @$data["_"]);
					} else {
						$q = $q->OrWhere($table . $s, "like", "%" . (@$data["_"] ? $data["_"] : "") . "%");
					}
				}
				return $q;
			});
		}

		foreach ($resource->lenses() as $len) {
			$field = $len["field"];
			if (isset($data[$field])) {
				$value = @$data[$field];
				if ((string) $len["value"] == $value) {
					if (!@$len["handler"]) {
						$query = $query->where($field, $value);
					} else {
						$query = $query->where(function ($q) use ($len, $value) {
							return $len["handler"]($q, $value);
						});
					}
				}
			}
		}
		return $this->makeSorterHandler($resource, $query, $orderBy, $orderType);
	}

	public function clone($resource, $code)
	{
		$resource = ResourcesHelpers::find($resource);
		return $resource->cloneMethod($code);
	}

	public function getResourceCrudContent($resource, Request $request)
	{
		$resource = ResourcesHelpers::find($resource);
		$page_type = request("type");
		if ($page_type == "create") {
			$data = $this->getResourceCreateCrudContent($resource, $request);
		} else {
			$content = $resource->model->findOrFail($request["id"]);
			$data = $this->getResourceEditCrudContent($content, $resource, $request);
		}
		return ["resource" => $resource->serialize($page_type), "data" => $data];
	}

	protected function getResourceEditCrudContent($content, $resource, Request $request)
	{
		$data = $this->makeCrudData($resource, $content);
		$data["page_type"] = "Edição";
		return $data;
	}

	public function edit($resource, $code, Request $request)
	{
		request()->request->add(["page_type" => "edit"]);
		$resource = ResourcesHelpers::find($resource);
		$content = $resource->model->findOrFail($code);
		if (!$resource->canUpdateRow($content) || !$resource->canUpdate()) {
			abort(403);
		}
		$data = $this->getResourceEditCrudContent($content, $resource, $request);
		$params = @$request["params"] ? $request["params"] : [];
		return $resource->editMethod($params, $data, $content);
	}

	protected function getResourceCreateCrudContent($resource, Request $request)
	{
		if (!$resource->canCreate()) abort(403);
		$data = $this->makeCrudData($resource);
		$data["page_type"] = "Cadastro";
		return $data;
	}

	public function create($resource, Request $request)
	{
		request()->request->add(["page_type" => "create"]);
		$params = @$request["params"] ? $request["params"] : [];
		$resource = ResourcesHelpers::find($resource);
		if (!@$resource->canCreate()) abort(403);
		$data = $this->getResourceCreateCrudContent($resource, $request);
		return $resource->createMethod($params, $data);
	}

	public function import($resource)
	{
		$resource = ResourcesHelpers::find($resource);
		if (!$resource->canImport()) {
			abort(403);
		}
		$data = $this->makeImportData($resource);
		return view("vStack::resources.import", compact('data'));
	}

	public function importSheetTemplate($resource)
	{
		$resource = ResourcesHelpers::find($resource);
		if (!$resource->canImport()) {
			abort(403);
		}
		$filename = $resource->id . "_" . Carbon::now()->format('Y_m_d_H_i_s') . '_' . Auth::user()->tenant->name . ".xlsx";
		$exporter = new DefaultGlobalExporter($this->getImporterCollumns($resource));
		Excel::store($exporter, $filename, "local");
		$full_path = storage_path("app/$filename");
		return response()->download($full_path)->deleteFileAfterSend(true);
	}

	protected function getImporterCollumns($resource)
	{
		$protected = [
			"created_at", "deleted_at", "updated_at", "email_verified_at",
			"confirmation_token", "recovery_token", "password", "tenant_id"
		];

		$columns = [];
		$importe_columns = $resource->importerColumns();
		$importe_columns = $importe_columns ? $importe_columns : $resource->getTableColumns();
		foreach ($importe_columns as $row) {
			if (!in_array($row, $protected)) {
				$columns[] = $row;
			}
		}
		return $columns;
	}

	protected function makeImportData($resource)
	{
		return [
			"resource" => [
				"resource_id"    => $resource->id,
				"label"          => $resource->label(),
				"singular_label" => $resource->singularLabel(),
				"route"          => $resource->route(),
				"columns"        => $this->getImporterCollumns($resource),
				"import_custom_crud_message" => $resource->importCustomCrudMessage(),
				"import_custom_map_step" => $resource->importCustomMapStep(),
			]
		];
	}

	public function checkFileImport($resource, Request $request)
	{
		$resource = ResourcesHelpers::find($resource);
		if (!$resource->canImport()) {
			abort(403);
		}

		$file = $request->file("file");
		if (!$file) {
			return ["success" => false, "message" => ["type" => "error", "text" => "Arquivo inválido..."]];
		}

		// 128 mb
		if ($file->getSize() > 134217728) {
			return ["success" => false, "message" => ["type" => "error", "text" => "Arquivo maior do que o permitido..."]];
		}

		$data = Excel::toArray(new HeadingRowImport, $file);
		$header = @$data[0][0];
		$header = array_filter($header ? $header : []);

		if (!count($header)) {
			return ["success" => false, "message" => ["type" => "error", "text" => "Cabeçalho da planilha nao encontrado"]];
		}

		return ["success" => true, "data" => $header];
	}

	public function importSubmit($resource, Request $request)
	{
		$resource = ResourcesHelpers::find($resource);
		if (!$resource->canImport()) {
			abort(403);
		}

		$data = $request->all();
		$file = $data["file"];
		if (!$file) {
			return ["success" => false, "message" => ["type" => "error", "text" => "Arquivo inválido..."]];
		}

		if ($file->getSize() > 137072) {
			return ["success" => false, "message" => ["type" => "error", "text" => "Arquivo maior do que o permitido..."]];
		}

		$config = json_decode($data["config"]);
		$fieldlist = $config->fieldlist;
		$filename = Auth::user()->tenant_id . "_" . uniqid() . ".xlsx";
		$filepath = $file->storeAs('local', $filename);
		$user = Auth::user();
		$user_code = $user->code;
		$tenant_id = in_array("tenant_id", array_keys((array)$fieldlist)) ? null : $user->tenant_id;

		$extra_data = $resource->prepareImportData($data);
		if (!@$extra_data["success"]) {
			return $extra_data;
		} else {
			$extra_data = @$extra_data["data"];
		}

		dispatch(function () use ($filepath, $resource, $user_code, $fieldlist, $tenant_id, $user, $extra_data) {
			$importer_data = compact('filepath', 'extra_data', 'user_code', 'resource', 'fieldlist', 'filepath', 'tenant_id');
			$resource->importMethod($importer_data);
		})->onQueue(Vstack::queue_resource_import());

		return ["success" => true];
	}

	private function prepareExportSheet($originalQuery, $resource, $data)
	{
		$user = Auth::user();
		$total = $originalQuery->count();
		if ($total <= 30) {
			$per_page = 10;
		}
		if ($total > 30 && $total <= 100) {
			$per_page = 30;
		}
		if ($total > 100 && $total <= 300) {
			$per_page = 50;
		}
		if ($total > 300  && $total <= 500) {
			$per_page = 100;
		}
		if ($total > 500  && $total <= 800) {
			$per_page = 300;
		}
		if ($total > 800) {
			$per_page = 500;
		}

		$disabled_columns = [];
		foreach ($data['columns'] as $key => $value) {
			if (!@$value["enabled"]) {
				$disabled_columns[] = $key;
			}
		}

		$config = ResourceConfig::where("data->user_id", $user->id)
			->where("resource", $resource->id)
			->where("config", "resource_export_disabled_columns")
			->first();

		$config = @$config->id ? $config : new ResourceConfig;
		$config->resource = $resource->id;
		$config->config = "resource_export_disabled_columns";
		$_data = @$config->data ?? (object)[];
		$_data->user_id = $user->id;
		$_data->disabled_columns = $disabled_columns;
		$config->data = $_data;
		$config->save();

		return [
			"per_page" => $per_page,
			"total" => $total,
			"action" => "set_totals",
			"current_page" => 1,
			"last_page" => ceil($total / $per_page),
			"disabled_columns" => $disabled_columns
		];
	}

	public function export($resource, Request $request)
	{
		request()->request->add(["page_type" => "exporting_report"]);
		$resource = ResourcesHelpers::find($resource);

		if (!$resource->canExport()) {
			abort(403);
		}

		$data = $request->all();
		$_request = new Request();
		$_request->setMethod('POST');

		$params = [];
		foreach ($data["get_params"] as $key => $value) {
			$params[$key] = $value;
		}

		$_request->request->add($params);
		$query = $this->getData($resource, $_request);

		$current_page = data_get($data, "exporting.current_page");
		$query = $resource->prepareQueryToExport($query->select("*"));

		if (!$current_page) {
			$prepared = $this->prepareExportSheet($query, $resource, $data);
			return response()->json($prepared);
		}

		$current_page = data_get($data, "exporting.current_page");
		$per_page = data_get($data, "exporting.per_page");
		$last_page = data_get($data, "exporting.last_page");

		$results = $query->select("*")->paginate($per_page, ['*'], 'page', $current_page);

		$processed_row = $this->processExportRow($resource, $results, $data);

		$action = $current_page === $last_page ? "finish" : "next_page";
		return response()->json(["action" => $action, "processed_row" => $processed_row]);
	}

	protected function processExportRow($resource, $results, $data)
	{
		$vstack_controller = new VstackController;
		$columns = data_get($data, 'columns');
		$processed_rows = [];
		foreach ($results as $row) {
			$result = (array_filter(array_map(function ($key)  use ($row, $columns, $vstack_controller, $resource) {
				$enabled = data_get($columns, $key . ".enabled");
				if ($enabled) {
					return $vstack_controller->getColumnIndex($resource->exportColumns(), $row, $key);
				}
			}, array_keys($columns))));
			$processed_rows[] = array_values($result);
		}
		return $processed_rows;
	}

	public function sheetImportRow($rows, $params, $importer)
	{
		extract($params);
		$qty = 0;
		try {
			DB::beginTransaction();
			foreach ($rows as $key => $row_values) {
				if ($key == 0) {
					continue;
				}
				$row_values = $row_values->toArray();
				$new = [];
				foreach ($fieldlist as $field => $row_key) {
					if ($row_key == "_IGNORE_") continue;
					$value = @$row_values[array_search($row_key, $importer->headers)];
					if (!$value) {
						continue;
					}
					$new[$field] = $value;
				}
				if ($tenant_id) {
					$new["tenant_id"] = $tenant_id;
				}
				$resource->importRowMethod($new, $extra_data);
				$qty++;
			}
			DB::commit();
			$importer->setResult([
				'success' => true,
				'qty' => $qty
			]);
		} catch (\Exception $e) {
			$importer->setResult([
				'success' => false,
				'error' => [
					"message" => $e->getMessage(),
					"line" => $key
				]
			]);
			DB::rollback();
		}
	}

	public function beforeDestroy($resource, $code, Request $request)
	{
		$resource = ResourcesHelpers::find($resource);
		$action = $resource->beforeDelete()[$request["index"]];
		$result = $action["handler"]($code);
		return response()->json($result);
	}

	public function destroy($resource, $code, Request $request)
	{
		$method = $request->method();
		if (!in_array($method, ['DELETE', 'POST'], $method)) {
			abort(404);
		}
		if ($method == "POST") {
			if ($request->input_origin) {
				request()->request->add(["input_origin" => $request->input_origin]);
			}
		}
		$resource = ResourcesHelpers::find($resource);
		$content = $resource->model->findOrFail($code);
		if (!$resource->canDeleteRow($content) || !$resource->canDelete()) {
			abort(403);
		}
		$result = $resource->destroyMethod($content);
		return $result;
	}

	public function destroyField($resource, $code)
	{
		$resource = ResourcesHelpers::find($resource);
		if (!$resource->canDelete()) abort(403);
		$content = $resource->model->findOrFail($code);
		if ($content->delete()) return ["success" => true];
		return ["success" => false];
	}

	public function view(Request $request, $resource, $code)
	{
		request()->request->add(["page_type" => "view"]);
		$resource = ResourcesHelpers::find($resource);
		$content = $resource->model->findOrFail($code);
		if (!$resource->canViewRow($content) || !$resource->canView()) {
			abort(403);
		}
		if (request()->is_api) {
			return $content;
		}
		$data = $this->getResourceEditCrudContent($content, $resource, $request);
		$params = @$request["params"] ? $request["params"] : [];
		$data["page_type"] = "Visualização";

		return $resource->viewMethod($params, $data, $content);
	}

	protected function makeCrudData($resource, $content = null)
	{
		request()->request->add(["content" => @$content]);
		return [
			"id"          => @$content->id,
			"fields"      => $this->makeCrudDataFields($content, $resource->fields()),
			"store_route" => route('resource.store', ["resource" => $resource->id]),
			"checkout_route" => route('resource.check', ["resource" => $resource->id]),
			"list_route"  => route('resource.index', ["resource" => $resource->id]),
			"resource_id" => $resource->id
		];
	}

	protected function makeCrudDataFields($content, $cards)
	{
		foreach ($cards  as $card) {
			foreach ($card->inputs  as $input) {
				switch ($input->options["type"]) {
					case "upload":
						if (!@$content->casts[$input->options["field"]]) {
							$input->options["value"] = null;
							$field_value = [];
							if ($content && $content->{$input->options["field"]}) {
								$field_value = @$content->{$input->options["field"]} ?? [];
							}
							$field_value = array_map(function ($row) {
								return @$row->url;
							}, $field_value);
							$input->options["value"] = @$field_value;
						} else {
							$value = @$content->{$input->options["field"]};
							if (!is_array($value)) {
								$value = [$value];
							}
							$input->options["value"] = $value ? $value : null;
						}
						break;
					case "html_editor":
						$value = @$content->{$input->options["field"]};
						$input->options["value"] = $value ? $value : "";
						break;
					case "custom_component":
						$value = @$content->{$input->options["field"]};
						$input->options["value"] = $value ? $value : null;
						break;
					default:
						$input->options["value"] = ($input->options["field"] == "password") ? null : @$content->{$input->options["field"]};
						break;
				}
			}
		}
		return $cards;
	}

	public function checkStore(Request $request)
	{
		$resource = ResourcesHelpers::find($request->resource_id);
		$data = $resource->beforeStore($request->all());
		return $data;
	}

	public function store(Request $request)
	{
		if ($request->input_origin) {
			request()->request->add(["input_origin" => $request->input_origin]);
		}
		$data = $request->all();
		if (!@$data["resource_id"]) {
			abort(404);
		}
		$resource = ResourcesHelpers::find($data["resource_id"]);
		if (@$data["id"]) {
			request()->request->add(["page_type" => "edit"]);
			if (!$resource->canUpdate()) {
				abort(403);
			}
		} else {
			request()->request->add(["page_type" => "create"]);
			if (!$resource->canCreate()) {
				abort(403);
			}
		}
		$id = @$data["id"];
		if ($id) {
			$content = $resource->getModelInstance()->find($id);
			request()->request->add(["content" => @$content]);
		}
		$validation_custom_message =  $resource->getValidationRuleMessage();
		$validator = Validator::make($request->all(), $resource->getValidationRule(), @$validation_custom_message ?? []);

		if ($validator->fails()) {
			throw new HttpResponseException(response()->json(["errors" => $validator->errors()], 422));
		}

		$data = $request->except(["is_api", "resource_id", "id", "redirect_back", "clicked_btn", "page_type", "content", "input_origin"]);
		$data = $this->processStoreData($resource, $data);
		return $resource->storeMethod($id, $data);
	}


	public function storeField(Request $request)
	{
		$data = $request->all();
		if (!@$data["resource_id"]) abort(404);
		$resource = ResourcesHelpers::find($data["resource_id"]);
		if (@$data["id"]) {
			if (!$resource->canUpdate()) {
				abort(403);
			}
		}
		if (!@$data["id"]) {
			if (!$resource->canCreate()) {
				abort(403);
			}
		}
		$this->validate($request, $resource->getValidationRule());
		$target = @$data["id"] ? $resource->model->findOrFail($data["id"]) : new $resource->model();
		$data = $request->except(["resource_id", "id", "redirect_back"]);
		$data = $this->processStoreData($resource, $data);
		$target->fill($data["data"]);
		$target->save();
		$this->storeUploads($target, $data["upload"]);
		return ["success" => true, "route" => route('resource.index', ["resource" => $resource->id])];
	}

	private function isClass($target, $index)
	{
		try {
			return class_exists(get_class(@$target->{$index}));
		} catch (\Throwable $e) {
			return false;
		}
	}

	public function storeUploads($target, $relations)
	{
		foreach ($relations as $key => $values) {
			$target->refresh();
			if ($this->isClass(@$target, $key)) {
				@$target->{"create" . ucfirst($key)}($values);
			} else {
				$target->{$key} = $values;
				$target->save();
			}
		}
	}

	protected function processStoreData($resource, $data)
	{
		$result = ["data" => $data];
		$result = $this->getUploadsFields($resource, $result);
		unset($result["data"][""]);
		return $result;
	}

	protected function getUploadsFields($resource, $result)
	{
		$fields = [];
		foreach ($resource->fields() as $cards) {
			foreach ($cards->inputs as $field) {
				if ($field->options["type"] == "upload") {
					@$fields[$field->options["field"]] = $result["data"][$field->options["field"]];
					unset($result["data"][$field->options["field"]]);
				}
			}
		}
		$result["upload"] = $fields;
		return $result;
	}

	public function getPerPage($resource)
	{
		$per_page = @request()->per_page;
		$results_per_page = $resource->resultsPerPage();
		$per_page = is_array($results_per_page) ? ((in_array($per_page ? $per_page : [], $results_per_page)) ? $per_page : $results_per_page[0]) : $results_per_page;
		return $per_page;
	}

	public function option_list(Request $request)
	{
		$model_fields = @$request->model_fields ?? [];
		try {
			$model = app()->make($request["model"]);
			$select_raw = data_get($model_fields, "id", "") . " as id," . data_get($model_fields, "name", "") . " as name";
			$model = $model->selectRaw($select_raw);
			$model = $model->orderBy(data_get($model_fields, "name", ""), "asc");
			return ["success" => true, "data" => $model->get()];
		} catch (\Exception $e) {
			return ["success" => false, "data" => []];
		}
	}

	public function globalSearch(Request $request)
	{
		$data = [];
		$filter = $request["filter"];
		foreach (ResourcesHelpers::all() as $resource) {
			$keys = array_keys($resource);
			$resource = $resource[$keys[0]];
			if ($resource->globallySearchable() && $resource->canView()) {
				$search_indexes = $resource->search();
				$query = $resource->model->where("id", ">", 0);
				$query = $query->where(function ($q) use ($search_indexes, $filter) {
					foreach ($search_indexes as $index) $q = $q->OrWhere($index, "like", "%$filter%");
					return $q;
				});
				$label = $resource->singularLabel();
				foreach ($query->get() as $row) {
					$data[] = [
						"resource" => $label,
						"name"     => $row->name,
						"link"     => $resource->route() . "/" . $row->code
					];
				}
			}
		}
		return ["data" => $data];
	}

	public function upload(Request $request)
	{
		$default_disk = config("vstack.upload_disk", "local");
		$disk = Storage::disk($default_disk);
		if (@$request['file']) {
			$file = $request->file('file');
			$name = $file->getClientOriginalName();
			$file_name = ($default_disk == "local" ? "public/$name" : $name);
			$contents = file_get_contents($file->getRealPath());
			$disk->put($file_name, $contents);
			$path = $disk->url($name);
			return ["path" => $path];
		}
		if (@$request['files']) {
			$file = $request['files'][0];
			$name = $file->getClientOriginalName();
			$file_name = ($default_disk == "local" ? "public/$name" : $name);
			$contents = file_get_contents($file->getRealPath());
			$disk->put($file_name, $contents);
			$path = $disk->url($name);
			return [
				"data" => [
					$path
				]
			];
		}
		return ["path" => asset(str_replace("public", "storage", $request->file('file')->store('public')))];
	}

	public function fieldData($resource, Request $request)
	{
		$resource = ResourcesHelpers::find($resource);
		$params = $request->except(["redirect_back"]);
		$query = $resource->model->where("id", ">", 0);
		foreach ($params as $key => $value) $query = $query->where($key, $value);
		$data = $this->getData($resource, $request, $query);
		$data = $data->get();
		$params = $request->all();
		$crud_data = $this->fieldDataProcessCrudData($resource, $params);
		$rendered_data = [
			"resource_id" => $resource->id,
			"index_label" => @$resource->indexLabel(),
			"label" => $resource->label(),
			"singular_label" => $resource->singularLabel(),
			"can_create"  => $resource->canCreate(),
			"can_update"  => $resource->canUpdate(),
			"can_delete"  => $resource->canDelete(),
			"model_count" => $resource->model->count(),
			"store_button_label" => $resource->storeButtonLabel(),
			"no_results_found_text" => $resource->noResultsFoundText(),
			"data" => $data,
			"data_count" => $data->count(),
			"icon" => $resource->icon(),
			"nothing_stored_text" => $resource->nothingStoredText(),
			"nothing_stored_subtext" => $resource->nothingStoredSubText(),
			"crud_fields" =>  $crud_data,
			"params" => $params,
			"store_route" => route('resource.field.store', ["resource" => $resource->id]),
			"table" => $resource->table(),
			"destroy_route" => route("resource.field.destroy", ["resource" => $resource->id, "id" => "_replace_area_"])
		];
		return response()->json($rendered_data);
	}

	protected function fieldDataProcessCrudData($resource, $params)
	{
		$fields = [];
		$crud_data = $this->makeCrudData($resource);
		for ($i = 0; $i < count($crud_data["fields"]); $i++) {
			for ($y = 0; $y < count($crud_data["fields"][$i]->inputs); $y++) {
				$field = $crud_data["fields"][$i]->inputs[$y];
				if (@$params[$crud_data["fields"][$i]->inputs[$y]->options['field']] != null) {
					$field->options["value"] = $params[$crud_data["fields"][$i]->inputs[$y]->options['field']];
					$field->options["visible"] = false;
					$field->options["disabled"] = true;
				}
				$field->getView();
				$fields[] = $field;
			}
		}
		return $fields;
	}

	public function getTags($resource, $id)
	{
		$resource = ResourcesHelpers::find($resource);
		if (!@$resource->useTags()) {
			abort(403);
		}
		return DB::table('resource_tags_relation')
			->select('resource_tags.*')
			->join('resource_tags', 'resource_tags.id', 'resource_tags_relation.resource_tag_id')
			->where('resource_tags.tenant_id', Auth::user()->tenant_id)
			->where('resource_tags_relation.relation_id', $id)
			->where('resource_tags.model', get_class($resource->model))
			->get();
	}

	public function tagOptions($resource)
	{
		$resource = ResourcesHelpers::find($resource);
		if (!@$resource->useTags()) {
			abort(403);
		}
		return  Tag::where("model", get_class($resource->model))->get();
	}

	public function destroyTag($resource, $resource_id, $tag_id)
	{
		$resource = ResourcesHelpers::find($resource);
		if (!@$resource->useTags()) {
			abort(403);
		}
		TagRelation::where("resource_tag_id", $tag_id)->where("relation_id", $resource_id)->delete();
		if (TagRelation::where("resource_tag_id", $tag_id)->count() <= 0) Tag::where("id", $tag_id)->delete();
		return ["success" => true];
	}

	public function addTag($resource, $id, Request $request)
	{
		$resource = ResourcesHelpers::find($resource);
		if (!@$resource->useTags()) abort(403);
		$class_name = get_class($resource->model);
		$tag = $this->getTag($class_name, @$request["name"], $resource);
		$relations = TagRelation::where("resource_tag_id", $tag->id)->where("relation_id", $id);
		if ($relations->count() > 0) return $tag;
		$created = TagRelation::create([
			"resource_tag_id" => $tag->id,
			"relation_id" => $id,
			"model" => $class_name
		]);
		return $created->tag;
	}

	protected function getTag($model_class, $name, $resource)
	{
		$colors = $resource->tagColors();
		$old_tag = Tag::where("model", $model_class)->where("name", $name)->first();
		if ($old_tag) {
			return $old_tag;
		}
		return Tag::create([
			"model" => $model_class,
			"name" => $name,
			"color" => $colors[rand(0, count($colors) - 1)]
		]);
	}

	public function handlerAction($resource, $action_id, Request $request)
	{
		$resource = ResourcesHelpers::find($resource);
		$action = current(array_filter($resource->Actions(), function ($action) use ($action_id) {
			return $action->id == $action_id;
		}));
		if (!@$action->id) abort(404);
		return $action->handler($request);
	}

	public function storeWizardStepValidation(Request $request)
	{
		request()->request->add(["page_type" => $request["page_type"]]);
		$resource = ResourcesHelpers::find($request["resource_id"]);
		$rules = $resource->getValidationRule($request["wizard_step"]);
		$request->validate($rules);
		return ["success" => true];
	}

	public function getResource($resource_id, Request $request)
	{
		request()->request->add(["is_api" => true]);
		$result = $this->index($resource_id, $request);
		return response()->json($result);
	}

	public function findByCode($resource_id, $code, Request $request)
	{
		request()->request->add(["is_api" => true]);
		$decoded = \Hashids::decode($code);
		$id = @$decoded[0] ?? $code;
		$result = $this->view($request, $resource_id, $id);
		return response()->json($result);
	}

	public function apiStore($resource_id, $id, $type, Request $request)
	{
		request()->request->add(["is_api" => true, "resource_id" => $resource_id, "id" => @$id, "page_type" => $type]);
		$result = $this->store($request);
		return $result;
	}

	public function editResource($resource_id, $code, Request $request)
	{
		$id = @$decoded[0] ?? $code;
		$result = $this->apiStore($resource_id, $id, "edit", $request);
		return response()->json(data_get($result, "model"));
	}

	public function createResource($resource_id, Request $request)
	{
		$result = $this->apiStore($resource_id, null, "create", $request);
		return response()->json(data_get($result, "model"));
	}

	public function destroyResource($resource_id, $code, Request $request)
	{
		$id = @$decoded[0] ?? $code;
		$result = $this->destroy($resource_id, $id, $request);
		return response()->json(data_get($result, "success"));
	}

	public function apiLogin(Request $request)
	{
		$credentials = $request->only('email', 'password');
		if (Auth::attempt($credentials, (@$request['remember'] ? true : false))) {
			$user = Auth::user();
			$jwt = Vstack::encodeJWT(["id" => $user->id, "tenant_id" => $user->tenant_id, "name" => $user->name, "email" => $user->email]);
			return response()->json(["token" => $jwt]);
		}
		return response()->json("Invalid credentials", 401);
	}

	public function resource_tree(Request $request)
	{
		request()->request->add(["input_origin" => "resource-tree"]);

		$resource = ResourcesHelpers::find($request->parent_resource);
		$field = null;
		$cards = $resource->fields();
		foreach ($cards as $card) {
			foreach ($card->inputs as $input) {
				if (data_get($input, 'options.type') == "resource-tree" && data_get($input, 'options.resource') == $request->resource) {
					$field = $input;
					break;
				}
			}
		}

		$resource_field = ResourcesHelpers::find(data_get($field, "options.resource"));
		$inputOptions = data_get($field, "options");
		$inputDisabled = data_get($inputOptions, "disabled", false);
		$inputFieldsQtyFields = count($resource_field->tree_fields());
		$tree = $this->makeRecursiveTree($resource_field, data_get($field, "options"), $resource->id, $inputFieldsQtyFields, $inputDisabled);
		return $tree;
	}

	private function makeRecursiveTree($resource, $options, $parent_resource, $qty_fields, $disabled)
	{
		request()->request->add(["input_origin" => "resource-tree"]);
		$children = [];
		$cards = $resource->fields();
		$children = [];
		foreach ($cards as $card) {
			foreach ($card->inputs as $input) {
				if (data_get($input, 'options.type') == "resource-tree") {
					$resource_id = data_get($input, "options.resource");
					$resource_input = ResourcesHelpers::find($resource_id);
					$inputOptions = data_get($input, "options");
					$inputParentResource = data_get($input, "options.parent_resource");
					$inputDisabled = data_get($inputOptions, "disabled", false);
					$inputFieldsQtyFields = count($resource_input->tree_fields());
					$children = $this->makeRecursiveTree($resource_input, $inputOptions, $inputParentResource, $inputFieldsQtyFields, $inputDisabled);
				}
			}
		}
		$fields[] = [
			"acl" => [
				"delete" => $resource->canDelete(),
				"create" => $resource->canCreate(),
				"update" => $resource->canUpdate(),
			],
			"disabled" => $disabled,
			"parent_resource" => $parent_resource,
			"relation" => data_get($options, "relation"),
			"foreign_key" => data_get($options, "foreign_key", $parent_resource . "_id"),
			"resource" => data_get($options, "resource"),
			"template_code" => data_get($options, "template_code"),
			"label_index" => data_get($options, "label_index", "name"),
			"template" => data_get($options, "template"),
			"qty_fields" => $qty_fields,
			"label" => $resource->label(),
			"singular_label" => $resource->singularLabel(),
			"children" => $children
		];

		return $fields;
	}

	public function resource_tree_items(Request $request)
	{
		request()->request->add(["input_origin" => "resource-tree"]);
		$resource = ResourcesHelpers::find($request->resource);
		if (!$resource->canViewList()) {
			abort(404);
		}
		$parent_resource = ResourcesHelpers::find($request->parent_resource);
		$parent_model = $parent_resource->getModelInstance();
		$parent = $parent_model->find($request->parent_id);
		$query = $parent->{$request->relation}();
		$query = $resource->resourceTreeLoadItemsFilter($request, $query);
		$items = $query->paginate($resource->resourceTreePerPage());
		return  $items;
	}

	public function resource_tree_items_crud(Request $request)
	{
		$resource = ResourcesHelpers::find($request->resource);
		$fields = $resource->tree_fields();
		foreach ($fields as $field) {
			$field->view = $field->getView();
		}
		return  $fields;
	}
}
