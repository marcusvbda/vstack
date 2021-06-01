<?php

namespace marcusvbda\vstack\Controllers;

use App\Http\Controllers\Controller;
use ResourcesHelpers;
use Illuminate\Http\Request;
use Storage;
use marcusvbda\vstack\Services\Messages;
use Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use marcusvbda\vstack\Exports\{DefaultGlobalExporter, GlobalExporter};
use marcusvbda\vstack\Imports\GlobalImporter;
use Maatwebsite\Excel\HeadingRowImport;
use Excel;
use marcusvbda\vstack\Services\SendMail;
use marcusvbda\vstack\Models\{Tag, TagRelation};
use marcusvbda\vstack\Models\ResourceConfig;
use marcusvbda\vstack\Events\WebSocketEvent;
use marcusvbda\vstack\Vstack;

class ResourceController extends Controller
{
	public function report($resource, Request $request)
	{
		return $this->showIndexList($resource, $request, true);
	}

	protected function showIndexList($resource, Request $request, $report_mode = false)
	{
		$resource = ResourcesHelpers::find($resource);
		if ($report_mode && !$resource->canViewReport()) abort(403);
		else {
			if (!$resource->canViewList()) abort(403);
		}
		$data = $this->getData($resource, $request);
		$per_page = $this->getPerPage($resource);
		$data = $data->paginate($per_page);
		$data->map(function ($query) {
			$query->setAppends([]);
		});
		if (@$request["list_type"]) $this->storeListType($resource, $request["list_type"]);
		return view("vStack::resources.index", compact("resource", "data", "report_mode"));
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
		return $this->showIndexList($resource, $request);
	}

	public function getData($resource, Request $request, $query = null)
	{
		$table = $resource->model->getTable();
		if ($resource->isNoModelResource()) {
			return $resource->model->where("id", "<", 0);
		}
		$table = $resource->model->getTable() . ".";
		$data      = $request->all();
		$orderBy   = $table . Arr::get($data, 'order_by', "id");
		$orderType = Arr::get($data, 'order_type', "desc");

		$query     = $query ? $query : $resource->model->select($table . "id")->where($table . "id", ">", 0);
		$query->orderBy($orderBy, $orderType);

		foreach ($resource->filters() as $filter) $query = $filter->applyFilter($query, $data);
		$search = $resource->search();

		if (@$data["_"]) {
			$query = $query->where(function ($q) use ($search, $data, $table) {
				foreach ($search as $s) {
					if (is_callable($s)) $q = $s($q, @$data["_"]);
					else  $q = $q->OrWhere($table . $s, "like", "%" . (@$data["_"] ? $data["_"] : "") . "%");
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
		return $query->orderBy($orderBy, $orderType);
	}

	public function clone($resource, $code, Request $request)
	{
		$resource = ResourcesHelpers::find($resource);
		$content = $resource->model->findOrFail($code);
		if (!$resource->canCloneRow($content) || !$resource->canClone()) abort(403);
		$data = $this->makeCrudData($resource, $content);
		$data["page_type"] = "Clonagem";
		$params = @$request["params"] ? $request["params"] : [];
		return view("vStack::resources.crud", compact("resource", "data", "params", "content"));
	}


	public function create($resource, Request $request)
	{
		$params = @$request["params"] ? $request["params"] : [];
		$resource = ResourcesHelpers::find($resource);
		if (!$resource->canCreate()) abort(403);
		$data = $this->makeCrudData($resource);
		$data["page_type"] = "Cadastro";
		return view("vStack::resources.crud", compact("resource", "data", "params"));
	}

	public function import($resource)
	{
		$resource = ResourcesHelpers::find($resource);
		if (!($resource->canImport() && $resource->canCreate())) abort(403);
		$data = $this->makeImportData($resource);
		return view("vStack::resources.import", compact('data'));
	}

	public function importSheetTemplate($resource)
	{
		$resource = ResourcesHelpers::find($resource);
		if (!($resource->canImport() && $resource->canCreate())) abort(403);
		$file_extension = Vstack::resource_export_extension();
		$filename = $resource->id . "_" . Carbon::now()->format('Y_m_d_H_i_s') . '_' . Auth::user()->tenant->name . "." . $file_extension;
		$exporter = new DefaultGlobalExporter($this->getImporterCollumns($resource));
		Excel::store($exporter, $filename, "local");
		$full_path = storage_path("app/$filename");
		return response()->download($full_path)->deleteFileAfterSend(true);
	}

	protected function getImporterCollumns($resource)
	{
		$columns = [];
		foreach ($resource->getTableColumns() as $col) {
			if (!in_array($col, ["created_at", "deleted_at", "updated_at", "email_verified_at", "confirmation_token", "recovery_token", "password", "tenant_id"])) $columns[] = $col;
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
				"columns"        => $this->getImporterCollumns($resource)
			]
		];
	}

	public function checkFileImport($resource, Request $request)
	{
		$resource = ResourcesHelpers::find($resource);
		if (!($resource->canImport() && $resource->canCreate())) abort(403);
		$file = $request->file("file");
		if (!$file) return ["success" => false, "message" => ["type" => "error", "text" => "Arquivo inválido..."]];
		if ($file->getSize() > 137072) return ["success" => false, "message" => ["type" => "error", "text" => "Arquivo maior do que o permitido..."]];
		$data = Excel::toArray(new HeadingRowImport, $file);
		$header = @$data[0][0];
		if (!@$data[0][0])
			return ["success" => false, "message" => ["type" => "error", "text" => "Cabeçalho da planilha nao encontrado"]];
		return ["success" => true, "data" => $header];
	}

	public function importSubmit($resource, Request $request)
	{
		$resource = ResourcesHelpers::find($resource);
		if (!($resource->canImport() && $resource->canCreate())) abort(403);
		$data = $request->all();
		$file = $data["file"];
		if (!$file) return ["success" => false, "message" => ["type" => "error", "text" => "Arquivo inválido..."]];
		if ($file->getSize() > 137072) return ["success" => false, "message" => ["type" => "error", "text" => "Arquivo maior do que o permitido..."]];

		$config = json_decode($data["config"]);
		$fieldlist = $config->fieldlist;
		$file_extension = Vstack::resource_export_extension();
		$filename = Auth::user()->tenant_id . "_" . uniqid() . "." . $file_extension;
		$filepath = $file->storeAs('local', $filename);
		$user = Auth::user();
		$tenant_id = in_array("tenant_id", array_keys((array)$fieldlist)) ? null : $user->tenant_id;
		dispatch(function () use ($resource, $fieldlist, $filepath, $tenant_id, $user) {
			$importer = new GlobalImporter($filepath, ResourceController::class, 'sheetImportRow', compact('resource', 'fieldlist', 'filepath', 'tenant_id'));
			Excel::import($importer, $importer->getFile());
			$result = $importer->getResult();
			if (@$result["success"]) {
				$message = "Foi importado com sucesso sua planilha de " . $resource->label() . ". (" . $result['qty'] . " Registro" . ($result['qty'] > 1 ? 's' : '') . ")";
			} else {
				$message = "Erro na importação de planilha de " . $resource->label() . " ( " . $result["error"]['message'] . " )";
			}
			DB::table("notifications")->insert([
				"type" => 'App\Notifications\CustomerNotification',
				"notifiable_type" => 'App\User',
				"notifiable_id" => $user->id,
				"alert_type" => 'vstack_alert',
				"tenant_id" => $user->tenant_id,
				"created_at" => carbon::now(),
				"data" => json_encode([
					"message" => $message,
					"type" => @$result["success"] ? 'success' : 'error'
				]),
			]);
		})->onQueue(Vstack::queue_resource_import());


		return ["success" => true];
	}

	public function export($resource, Request $request)
	{
		$resource = ResourcesHelpers::find($resource);
		if (!$resource->canExport()) abort(403);
		$user = Auth::user();
		$data = $request->all();
		$_request = new Request();
		$_request->setMethod('POST');
		$params = [];
		foreach ($data["get_params"] as $key => $value) $params[$key] = $value;
		$_request->request->add($params);
		$count = $this->getData($resource, $_request)->count();
		$filter = $_request->all();
		$file_id = md5(uniqid());
		$path = '/public/vstack/resource_export/';

		$disabled_columns = [];
		foreach ($data['columns'] as $key => $value) {
			if (!@$value["enabled"]) {
				$disabled_columns[] = $key;
			}
		}
		$config = ResourceConfig::where("data->user_id", $user->id)->where("resource", $resource->id)->where("config", "resource_export_disabled_columns")->first();
		$config = @$config->id ? $config : new ResourceConfig;
		$config->resource = $resource->id;
		$config->config = "resource_export_disabled_columns";
		$_data = @$config->data ?? (object)[];
		$_data->user_id = $user->id;
		$_data->disabled_columns = $disabled_columns;
		$config->data = $_data;
		$config->save();
		return $this->exportSheetOrDispatch($user, $count, $filter, $resource, $data['columns'], $path, $file_id);
	}

	public function exportSheetOrDispatch($user, $count, $filter, $resource, $columns, $path, $file_id)
	{
		$email = $user->email;
		$config = new ResourceConfig;
		$config->resource = $resource->id;
		$config->config = "report_export_$file_id";
		$route = route('resource.export_download', ['resource' => $resource->id, 'file' => $file_id]);
		$file_extension = Vstack::resource_export_extension();
		$config->data = [
			"user_id" => $user->id,
			"path" => $path,
			"file_id" => $file_id,
			"file_extension" => $file_extension,
			"file_name" => $resource->id . '_' . Carbon::now()->format('YmdHis'),
			"status" => 'exporting',
			"due_date" => Carbon::now()->addDays(1),
			"route" => $route,
			"microtime" => [
				"start" => microtime(true),
				"end" => 0,
			]
		];
		$config->save();

		if ($count <= $resource->maxRowsExportSync()) {
			try {
				$exporter = new GlobalExporter($resource, $columns, $filter);
				Excel::store($exporter, $path . $file_id . '.' . $file_extension, "local");
				$message = "Relatório de " . $resource->label() . " exportada com sucesso";
				$_data = $config->data;
				$_data->status = "ready";
				$_data->microtime->end = microtime(true);
				$config->data = $_data;
				$config->save();
				Messages::send("success", $message);
				$route = route('resource.export_download', ['resource' => $resource->id, 'file' => $file_id, 'destroy' => true]);
				return ['success' => true, 'url' => $route];
			} catch (\Exception $e) {
				$message = "Erro na exportação de relatório de " . $resource->label() . " ( " . $e->getMessage() . " )";
				return ['success' => false, 'message_type' => 'error', 'message' => $message];
			}
		}
		dispatch(function () use ($user, $resource, $columns, $filter, $file_id, $email, $config, $path, $file_extension) {
			$_data = $config->data;
			$_data->status = "exporting";
			$config->data = $_data;
			$config->save();
			try {
				$exporter = new GlobalExporter($resource, $columns, $filter);
				Excel::store($exporter, $path . $file_id . '.' . $file_extension, "local");
				$route = route('resource.export_download_intercept', ['resource' => $resource->id, 'file' => $file_id]);
				$_data = $config->data;
				$_data->status = "ready";
				$_data->microtime->end = microtime(true);
				$_data->due_date = Carbon::now()->addDays(1);
				$config->data = $_data;
				$config->save();
				$html = view($resource->exportNotificationView(), compact('user', 'resource', 'route'))->render();
				broadcast(new WebSocketEvent("App.User." . $config->data->user_id, "notifications.exporting_status." . $config->id, [
					"config" => $config
				]));
				SendMail::to($email, "Relatório de " . $resource->label(), $html);
			} catch (\Exception $e) {
				$message = "Erro na exportação de relatório de " . $resource->label() . " ( " . $e->getMessage() . '  - on line ' . $e->getLine() . " )";
				$_data = $config->data;
				$_data->status = "error";
				$_data->error_message = $message;
				$config->data = $_data;
				$config->save();
				return ['success' => false, 'message' => $message];
			}
		})->onQueue(Vstack::queue_resource_export());
		$message = "Sua Relatório de " . $resource->label() . " está sendo exportado, e assim que o processo for concluido você será notificado e o arquivo será enviado em seu email (" . $email . "), isso pode levar alguns minutos.";
		Messages::send("info", $message);
		return ['success' => true];
	}

	public function exportDownload($resource, $file_id, Request $request)
	{
		try {
			$resource = ResourcesHelpers::find($resource);
			if (!$resource->canExport()) abort(403);
			$user = Auth::user();
			$config = ResourceConfig::where("data->user_id", $user->id)->where("resource", $resource->id)->where("config", "report_export_$file_id")->firstOrFail();
			$full_path = storage_path("app/public/vstack/resource_export/" . $config->data->file_id . "." . @$config->data->file_extension);
			if ($request->has('destroy')) $config->delete();
			return response()->download($full_path, $config->data->file_name . "." . @$config->data->file_extension)->deleteFileAfterSend($request->has('destroy'));
		} catch (\Exception $e) {
			abort(404);
		}
	}

	public function exportDownloadIntercept($resource, $file_id)
	{
		$resource = ResourcesHelpers::find($resource);
		$route = route('resource.export_download', ['resource' => $resource->id, 'file' => $file_id]);
		return View("vStack::resources.email_download", compact("route", "resource"));
	}

	public function sheetImportRow($rows, $params, $importer)
	{
		extract($params);
		$qty = 0;
		try {
			DB::beginTransaction();
			foreach ($rows as $key => $row_values) {
				if ($key == 0) continue;
				$row_values = $row_values->toArray();
				$new = [];
				foreach ($fieldlist as $field => $row_key) {
					if ($row_key == "_IGNORE_") continue;
					$value = @$row_values[array_search($row_key, $importer->headers)];
					if (!$value) continue;
					$new[$field] = $value;
				}
				$new_model = @$new["id"] ? $resource->model->findOrFail($new["id"]) : new $resource->model;
				if ($tenant_id) {
					$new["tenant_id"] = $tenant_id;
				}
				$new_model->fill($new);
				$new_model->save();
				unset($new_model, $row_values, $new);
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

	public function edit($resource, $code, Request $request)
	{
		$resource = ResourcesHelpers::find($resource);
		$content = $resource->model->findOrFail($code);
		if (!$resource->canUpdateRow($content) || !$resource->canUpdate()) abort(403);
		$data = $this->makeCrudData($resource, $content);
		$data["page_type"] = "Edição";
		$params = @$request["params"] ? $request["params"] : [];
		return view("vStack::resources.crud", compact("resource", "data", "params", "content"));
	}

	public function beforeDestroy($resource, $code, Request $request)
	{
		$resource = ResourcesHelpers::find($resource);
		$action = $resource->beforeDelete()[$request["index"]];
		$result = $action["handler"]($code);
		return response()->json($result);
	}

	public function destroy($resource, $code)
	{
		$resource = ResourcesHelpers::find($resource);
		$content = $resource->model->findOrFail($code);
		if (!$resource->canDeleteRow($content) || !$resource->canDelete()) abort(403);
		if ($content->delete()) {
			Messages::send("success", "Registro excluido com sucesso !!");
			return ["success" => true, "route" => $resource->route()];
		}
		Messages::send("error", " Erro ao excluir com " . $resource->singularLabel() . " !!");
		return ["success" => false,  "route" => $resource->route()];
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
		$resource = ResourcesHelpers::find($resource);
		$content = $resource->model->findOrFail($code);
		if (!$resource->canViewRow($content) || !$resource->canView()) abort(403);
		$data = $this->makeViewData($content->code, $resource, $content);
		$data["page_type"] = "Visualização";
		$params = @$request["params"] ? $request["params"] : [];
		return view("vStack::resources.view", compact("resource", "data", "params", "content"));
	}

	public function makeViewData($code, $resource, $content = null)
	{
		$route = $resource->route();
		return [
			"fields"        => $this->makeViewDataFields($content, $resource->fields()),
			"can_update"    => $resource->canUpdate(),
			"can_delete"    => $resource->canDelete(),
			"route"         => $route . "/" . $code,
			"update_route"  => $route . "/" . $code . "/edit",
			"route_destroy" => $route . "/" . $code . "/destroy",
		];
	}

	protected function makeViewDataFields($content, $fields)
	{
		$data = [];
		if (!$content) return $fields;
		foreach ($fields  as $card) {
			$_card = [
				"label"  => $card->label,
				"inputs" => []
			];
			foreach ($card->inputs  as $field) {
				if (!in_array(@$field->options["field"], ["password", "password_confirmation"])) {
					switch ($field->options["type"]) {
						case "text":
							$_card["inputs"][$field->options["label"]] = @$content->{$field->options["field"]};
							break;
						case "check":
							$_card["inputs"][$field->options["label"]] = @$content->{$field->options["field"]} ? '<span class="badge badge-success">Sim</span>' : '<span class="badge badge-danger">Não</span>';
							break;
						case "custom_component":
							$_card["inputs"][$field->options["label"]] = @$field->view;
							break;
						case "belongsTo":
							if (@$field->options["model"]) {
								$model = $field->options["model"];
								$value = @app()->make($model)->find($content->{$field->options["field"]})->name;
								$_card["inputs"][$field->options["label"]] = $value;
							} else $_card["inputs"][$field->options["label"]] = $content->{$field->options["field"]};
							break;
						case "belongsToMany":
							$value = implode(",", $content->{$field->options["field"]}->pluck(@$field->options["pluck_value"] ? $field->options["pluck_value"] : "value")->toArray());
							$_card["inputs"][$field->options["label"]] = $value;
							break;
						case "morphsMany":
							$value = implode(",", $content->{$field->options["field"]}->pluck("value")->toArray());
							$_card["inputs"][$field->options["label"]] = $value;
							break;
						case "upload":
							if (!@$content->casts[$field->options["field"]])
								$array = $content ? @$content->{$field->options["field"]}->pluck("value")->toArray() : [];
							else
								$array = @$content->{$field->options["field"]} ? @$content->{$field->options["field"]} : null;

							$array = $array ? $array : [];
							if (!is_array($array)) $array = [$array];
							foreach ($array as $row) {
								@$_card["inputs"][$field->options["label"]] .= "<p class='my-0'><a class='link preview' target='_BLANK' href='" . $row . "'>" . $row . "</a></p>";
							}
							break;
						case "url":
							$_card["inputs"][$field->options["label"]] = "<a class='link preview' target='_BLANK' href='" . @$content->{$field->options["field"]} . "'>" . @$content->{$field->options["field"]} . "</a>";
							break;
						case "resource-field":
							$_resource = ResourcesHelpers::find(strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $field->options["resource"])));
							foreach ($field->options["params"] as $key => $value) $params[$key] = @$content->{$value} ? $content->{$value} : $value;
							$view = $field->getView();
							$target = substr($view, strpos($view, ":params='"), strpos($view, "' end_params"));
							$view = str_replace($target, ":params='" . json_encode($params) . "' />", $view);
							$_card["inputs"]["IGNORE__" . $_resource->label()] = $view;
							break;
						case "custom":
							$custom_params = "";
							foreach ($field->options['params'] as $custom_key => $custom_value) {
								eval("\$custom_value = \"$custom_value\";");
								$custom_params .= ":$custom_key='$custom_value' ";
							}
							$custom_oldView = $field->view;
							$custom_view = str_replace(" />", " $custom_params  />", $custom_oldView);
							$_card["inputs"]["IGNORE__" . uniqid()] = $custom_view;
							break;
						default:
							$_card["inputs"][$field->options["label"]] = @$content->{$field->options["field"]};
							break;
					}
				}
			}
			$data[] = $_card;
		}
		return $data;
	}

	protected function makeCrudData($resource, $content = null)
	{
		request()->request->add(["content" => @$content]);
		return [
			"id"          => @$content->id,
			"fields"      => $this->makeCrudDataFields($content, $resource->fields()),
			"store_route" => route('resource.store', ["resource" => $resource->id]),
			"list_route"  => route('resource.index', ["resource" => $resource->id]),
			"resource_id" => $resource->id
		];
	}

	protected function makeCrudDataFields($content, $cards)
	{
		foreach ($cards  as $card) {
			foreach ($card->inputs  as $input) {
				switch ($input->options["type"]) {
					case "belongsToMany":
						$input->options["value"] = $content ? @$content->{$input->options["field"]}->pluck("id")->toArray() : null;
						break;
					case "morphsMany":
						$input->options["value"] = $content ? @$content->{$input->options["field"]}->pluck("value")->toArray() : null;
						break;
					case "upload":
						if (!@$content->casts[$input->options["field"]])
							$input->options["value"] = $content ? @$content->{$input->options["field"]}->pluck("value")->toArray() : null;
						else {
							$view = $input->getView();
							$oldView = $view;
							$value = @$content->{$input->options["field"]};
							if (!is_array($value)) $value = [$value];
							$input->options["value"] = $value ? $value : null;
						}
						break;
					case "resource-field":
						$params = [];
						foreach ($input->options["params"] as $key => $value) $params[$key] = @$content->{$value} ? $content->{$value} : $value;
						$view = $input->getView();
						$oldView = $view;
						$target = substr($view, strpos($view, ":params='"), strpos($view, "' end_params"));
						$view = str_replace($target, ":params='" . json_encode($params) . "' />", $view);
						$input->view = $view;
						$card->view = str_replace($oldView, $view, $card->view);
						break;
					case "custom":
						if (@$content) {
							$params = "";
							foreach ($input->options['params'] as $key => $value) {
								eval("\$value = \"$value\";");
								$params .= ":$key='$value' ";
							}
							$oldView = $input->view;
							$view = str_replace(" />", " $params  />", $oldView);
							$card->view = str_replace($oldView, $view, $card->view);
						} else  $card->view = "";
						break;
					case "html_editor":
						$value = @$content->{$input->options["field"]};
						$input->options["value"] = $value ? $value : (object)["css" => "", "body" => ""];
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

	public function store(Request $request)
	{
		$data = $request->all();
		if (!@$data["resource_id"]) abort(404);
		$resource = ResourcesHelpers::find($data["resource_id"]);
		if (@$data["id"]) if (!$resource->canUpdate()) abort(403);
		if (!@$data["id"]) if (!$resource->canCreate()) abort(403);
		$validation_custom_message =  $resource->getValidationRuleMessage();
		$this->validate($request, $resource->getValidationRule(), @$validation_custom_message ? $validation_custom_message : []);
		$id = @$data["id"];
		$data = $request->except(["resource_id", "id", "redirect_back", "clicked_btn"]);
		$data = $this->processStoreData($resource, $data);
		return $resource->storeMethod($id, $data);
	}

	public function storeField(Request $request)
	{
		$data = $request->all();
		if (!@$data["resource_id"]) abort(404);
		$resource = ResourcesHelpers::find($data["resource_id"]);
		if (@$data["id"]) if (!$resource->canUpdate()) abort(403);
		if (!@$data["id"]) if (!$resource->canCreate()) abort(403);
		$this->validate($request, $resource->getValidationRule());
		$target = @$data["id"] ? $resource->model->findOrFail($data["id"]) : new $resource->model();
		$data = $request->except(["resource_id", "id", "redirect_back"]);
		$data = $this->processStoreData($resource, $data);
		$target->fill($data["data"]);
		$target->save();
		$this->storeBelongsToMany($target, $data["belongsToMany"]);
		$this->storeMorphsMany($target, $data["morphsMany"]);
		$this->storeUploads($target, $data["upload"]);
		return ["success" => true, "route" => route('resource.index', ["resource" => $resource->id])];
	}

	public function storeUploads($target, $relations)
	{
		$target->refresh();
		foreach ($relations as $key => $values) {
			if (is_callable($target->{$key})) {
				$target->{$key}()->delete();
				if ($values) {
					foreach ($values as $value) {
						$target->{$key}()->create(["value" => $value]);
					}
				}
			} else {
				$target->{$key} = $values;
				$target->save();
			}
		}
	}

	public function storeMorphsMany($target, $relations)
	{
		$target->refresh();
		foreach ($relations as $key => $values) {
			$target->{$key}()->delete();
			if ($values) {
				foreach ($values as $value) {
					$target->{$key}()->create(["value" => $value]);
				}
			}
		}
	}

	public function storeBelongsToMany($target, $relations)
	{
		$target->refresh();
		foreach ($relations as $key => $value) {
			$target->{$key}()->sync($value);
		}
	}

	protected function processStoreData($resource, $data)
	{
		$result = $this->getBelongsToManyFields($resource, $data);
		$result = $this->getMorphsManyFields($resource, $result);
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

	protected function getMorphsManyFields($resource, $result)
	{
		$fields = [];
		foreach ($resource->fields() as $cards) {
			foreach ($cards->inputs as $field) {
				if ($field->options["type"] == "morphsMany") {
					@$fields[$field->options["field"]] = $result["data"][$field->options["field"]];
					unset($result["data"][$field->options["field"]]);
				}
			}
		}
		$result["morphsMany"] = $fields;
		return $result;
	}

	protected function getBelongsToManyFields($resource, $data)
	{
		$fields = [];
		foreach ($resource->fields() as $cards) {
			foreach ($cards->inputs as $field) {
				if ($field->options["type"] == "belongsToMany") {
					$fields[$field->options["field"]] = $data[$field->options["field"]];
					unset($data[$field->options["field"]]);
				}
			}
		}
		return ["belongsToMany" => $fields, "data" => $data];
	}

	public function getPerPage($resource)
	{
		$results_per_page = $resource->resultsPerPage();
		$per_page = is_array($results_per_page) ? ((in_array(@$_GET['per_page'] ? $_GET['per_page'] : [], $results_per_page)) ? $_GET['per_page'] : $results_per_page[0]) : $results_per_page;
		return $per_page;
	}

	public function option_list(Request $request)
	{
		try {
			$model = app()->make($request["model"]);
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

	public function metricCalculate($resource, $key, Request $request)
	{
		$resource = ResourcesHelpers::find($resource);
		$metric = null;
		foreach ($resource->metrics() as $m) {
			if ($m->uriKey() == $key) {
				$metric = $m;
				break;
			}
		}
		if (!$metric) abort(404);
		return $metric->calculate($request);
	}

	public function upload(Request $request)
	{
		if (@$request['file']) {
			$url = $request['file'];
			$name = pathinfo($url, PATHINFO_FILENAME) . ".jpg";
			Storage::put(
				"public/$name",
				file_get_contents($url)
			);
			return ["path" => asset("public/storage/$name")];
		}
		if (@$request['files']) {
			$url = $request['files'];
			$name = pathinfo($url, PATHINFO_FILENAME) . ".jpg";
			Storage::put(
				"public/$name",
				file_get_contents($url)
			);
			return [
				"data" => [
					asset("public/storage/$name")
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
				if ($field->options["type"] == "resource-field") {
					$field->view  = str_replace("/>", "v-if='form.id' />", $field->view);
				}
				$fields[] = $field;
			}
		}
		return $fields;
	}

	public function getTags($resource, $id)
	{
		$resource = ResourcesHelpers::find($resource);
		// dd($id, get_class($resource->model));
		if (!@$resource->useTags()) abort(403);
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
		if (!@$resource->useTags()) abort(403);
		return  Tag::where("model", get_class($resource->model))->get();
	}

	public function destroyTag($resource, $resource_id, $tag_id)
	{
		$resource = ResourcesHelpers::find($resource);
		if (!@$resource->useTags()) abort(403);
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
		if ($old_tag) return $old_tag;
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
}