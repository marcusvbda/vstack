<?php

namespace marcusvbda\vstack;

use App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use marcusvbda\vstack\Controllers\ResourceController;
use marcusvbda\vstack\Events\WebSocketEvent;
use marcusvbda\vstack\Fields\{Card, Text};
use marcusvbda\vstack\Imports\GlobalImporter;
use marcusvbda\vstack\Models\Migration;
use marcusvbda\vstack\Services\Messages;

class Resource
{
	public $model = null;
	public $model_string  = null;
	public $debug = false;
	public $id    = [];
	public $parentResource    = [];
	public $row    = [];

	public function __construct($parentResource = null, $row = null)
	{
		$this->parentResource = $parentResource;
		$this->row = $row;
		$this->model_string = $this->model ? (is_object($this->model) ? $this->model->getMorphClass() : $this->model) : Migration::class;
		$this->model = App::make($this->model_string);
		$this->makeId();
	}

	public function isNoModelResource()
	{
		return $this->model->getMorphClass() === Migration::class;
	}

	public function singularLabel()
	{
		return $this->id;
	}

	public function resultsPerPage()
	{
		return [5, 10, 20, 50, 100];
	}

	public function menu()
	{
		return "Recursos";
	}

	public function menuIcon()
	{
		return "el-icon-menu";
	}

	public function globallySearchable()
	{
		return false;
	}

	public function label()
	{
		return $this->id;
	}

	public function icon()
	{
		return "";
	}

	public function table()
	{
		return ["id" => ["label" => "#"]];
	}

	public function listCardView()
	{
		return "vStack::resources.partials._list_cards";
	}

	public function filters()
	{
		return [];
	}

	public function indexLabel()
	{
		return "<span class='" . $this->icon() . " mr-2'></span>" . " Listagem de " . $this->label();
	}

	public function reportLabel()
	{
		return "<span class='" . $this->icon() . " mr-2'></span>" . " Relatório de " . $this->label();
	}

	public function storeBtnText()
	{
		return "Cadastrar";
	}

	public function storeButtonlabel()
	{
		return "<span class='el-icon-plus mr-2'></span>" . $this->storeBtnText();
	}

	public function importButtonlabel()
	{
		return "<span class='el-icon-upload2 mr-2'></span>Importar Planilha de " . $this->label();
	}

	public function importViewSettings()
	{
		$route_example = "/admin/" . $this->id . "/import/sheet_template";

		return [
			"page_title" => "Importação de " . $this->label(),
			"description" => '
				<div class="mt-3">
					Esta ferramenta permite importar ' . $this->label() . ' a partir de uma
					planilha.
				</div>
				<div>
					<a class="link" href="' . $route_example . '"> Clique aqui para efetuar o download
					</a>
					do modelo de importação
				</div>
			',
			"input_text" => "Escolha um arquivo xlsx do seu computador"
		];
	}

	public function importCustomImportStep()
	{
		return false;
	}

	public function importHeader($uploaded_header)
	{
		return $uploaded_header;
	}

	public function exportButtonlabel()
	{
		return "<span class='el-icon-download mr-2'></span>Exportar Relatório de " . $this->label();
	}

	public function noResultsFoundText()
	{
		return "Nenhum resultado encontrado";
	}

	public function nothingStoredText()
	{
		return "<h4>Nada cadastrado ainda...</h4>";
	}

	public function secondCrudBtn()
	{
		if (!$this->canUpdate()) return null;
		return [
			"field" => "save",
			"class" => "secondary",
			"content" => "<div class='flex items-center'>
							<i class='el-icon-success mr-2'></i>
							Salvar
						</div>"
		];
	}

	public function firstCrudBtn()
	{
		return [
			"size" => "small",
			"field" => "save_and_back",
			"class" => "primary",
			"content" => "<div class='flex items-center'>
							<i class='el-icon-arrow-left mr-2'></i>
							Salvar e Voltar
						</div>"
		];
	}

	public function nothingStoredSubText()
	{
		return "<small>Clique em <b>'" . $this->storeBtnText() . "'</b> para adicionar um novo registro</small>";
	}

	public function fields()
	{
		$fields = [];
		$columns = array_filter($this->getTableColumns(), function ($x) {
			if (!in_array($x, ["id", "confirmation_token", "recovery_token", "password", "deleted_at", "updated_at", "created_at", "remember_token"])) return $x;
		});
		foreach ($columns as $column) {
			$fields[] = new Text([
				"label" => $column, "field" => $column, "required" => true,
				"placeholder" => "", "rules" => "required|max:255"
			]);
		}
		return [new Card("Informações", $fields)];
	}

	public function tree_fields()
	{
		$cards = $this->fields();
		foreach ($cards as $card) {
			$fields = [];
			foreach ($card->inputs as $input) {
				$type = data_get($input, "options.type");
				if (!in_array($type, ["resource-tree"])) {
					$fields[] = $input;
				}
			}
			$card->inputs = $fields;
			$card->makeView();
		}
		return $cards;
	}

	public function exportColumns()
	{
		return [
			["label" => "id", "handler" => function ($row) {
				return @$row->id;
			}],
			["label" => "created_at", "handler" => function ($row) {
				return @$row->created_at;
			}],
		];
	}

	public function getTableColumns()
	{
		return $this->getModelInstance()->getConnection()->getSchemaBuilder()->getColumnListing($this->model->getTable());
	}

	public function importCustomCrudMessage()
	{
		return "Deixe a coluna ID em branco a não ser que seja um caso de alteração de registro, ai esta linha será alterada ao invés cadastrada";
	}

	public function importCustomMapStep()
	{
		return false;
	}

	public function lenses()
	{
		return [];
	}

	public function metrics()
	{
		return [];
	}

	public function customMetricOptions()
	{
		return [];
	}

	public function search()
	{
		return [];
	}

	public function report_route()
	{
		return route("resource.report", ["resource" => strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', (new \ReflectionClass($this))->getShortName()))]);
	}

	public function route()
	{
		return route("resource.index", ["resource" => strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', (new \ReflectionClass($this))->getShortName()))]);
	}

	private function makeId()
	{
		$aux =  explode("/", $this->route());
		$this->id = $aux[count($aux) - 1];
	}

	public function checkAclResource($row, $type)
	{
		$type = ucfirst($type);
		$acl_row = $this->{"can{$type}Row"}($row);
		$acl_resource = $this->{"can{$type}"}();
		return $acl_row && $acl_resource;
	}

	public function canViewList()
	{
		return true;
	}

	public function canUpdateRow($row)
	{
		return $this->canUpdate();
	}

	public function canDeleteRow($row)
	{
		return $this->canDelete();
	}

	public function importerValidatorRulesMessages()
	{
		return [];
	}

	public function importerValidatorRules(Request $request)
	{
		return [];
	}

	public function beforeDelete()
	{
		return [];
	}

	public function destroyMethod($content)
	{
		if ($content->delete()) {
			if (request("input_origin") != "resource-tree") {
				Messages::send("success", "Registro excluido com sucesso !!");
			}
			return ["success" => true, "route" => $this->route()];
		}
		if (request("input_origin") != "resource-tree") {
			Messages::send("error", " Erro ao excluir com " . $this->singularLabel() . " !!");
		}
		return ["success" => false,  "route" => $this->route()];
	}

	public function canCloneRow($row)
	{
		return $this->canClone();
	}

	public function canViewRow($row)
	{
		return $this->canView();
	}

	public function canView()
	{
		return false;
	}

	public function canClone()
	{
		return false;
	}

	public function canCreate()
	{
		return true;
	}

	public function canImport()
	{
		return true;
	}

	public function canExport()
	{
		return $this->canViewReport();
	}

	public function canUpdate()
	{
		return true;
	}

	public function canDelete()
	{
		return true;
	}

	public function resultsFoundLabel()
	{
		return "Resultados encontrados : ";
	}

	public function canViewReport()
	{
		return false;
	}

	public function exportNotificationView()
	{
		return "vStack::resources.mails.export_notification";
	}

	private function getCardFieldsRules($card)
	{
		$validation_rules = [];

		foreach ($card->inputs as $field) {
			$rules = @$field->options["rules"] ?? [];
			if (!is_array($rules)) {
				$rules = explode("|", $rules);
			}
			if (@$field->options["required"]) {
				$rules[] = "required";
			}
			$rules = is_array($rules) ? $rules : [$rules];
			$validation_rules[@$field->options["field"] ?? "*"] = array_filter($rules, function ($row) {
				return $row;
			});
		}
		return $validation_rules;
	}

	public function getValidationRule($card_index = "all")
	{
		$validation_rules = [];
		foreach ($this->fields() as $key => $card) {
			if ($card_index === "all" || $key === $card_index) {
				$validation_rules = array_merge($validation_rules, $this->getCardFieldsRules($card));
			}
		}
		return $validation_rules;
	}

	public function getValidationRuleMessage()
	{
		$validation_messages = [];
		foreach ($this->fields() as $card) {
			foreach ($card->inputs as $field) {
				if (@$field->options["custom_message"]) {
					foreach (is_Array($field->options["custom_message"]) ? $field->options["custom_message"] : [] as $key => $value) {
						$validation_messages[(@$field->options["field"] ?? "*") . "." . $key] = @$value;
					}
				}
			}
		}
		return $validation_messages;
	}

	public function beforeReportListSlot()
	{
		return false;
	}

	public function beforeListSlot()
	{
		return false;
	}

	public function afterReportListSlot()
	{
		return false;
	}

	public function afterListSlot()
	{
		return false;
	}

	public function beforeEditSlot()
	{
		return false;
	}

	public function afterEditSlot()
	{
		return false;
	}

	public function beforeCreateSlot()
	{
		return false;
	}

	public function afterCreateSlot()
	{
		return false;
	}

	public function beforeViewSlot()
	{
		return false;
	}

	public function afterViewSlot()
	{
		return false;
	}

	public function useTags()
	{
		return false;
	}

	public function canCreateReportTemplates()
	{
		return true;
	}

	public function reportLimitTemplates()
	{
		return 5;
	}

	public function actions()
	{
		return [];
	}

	public function viewListBlade()
	{
		return "vStack::resources.partials._default_table";
	}

	public function viewReportBlade()
	{
		return "vStack::resources.partials._default_report_table";
	}

	public function makeIndexContent($data)
	{
		return view("vStack::resources.partials._default_index", $data);
	}

	public function viewBlade()
	{
		return "vStack::resources.partials._default_view";
	}

	public function createBlade()
	{
		return "vStack::resources.partials._default_crud";
	}

	public function editBlade()
	{
		return "vStack::resources.partials._default_crud";
	}

	public function maxWaitingReportsByUser()
	{
		return 5;
	}

	public function tableAfterRow($row)
	{
		return false;
	}

	public function useRawContentOnList()
	{
		return false;
	}

	public function showRightActionsColumn()
	{
		return $this->canView() || $this->canUpdate() || $this->canDelete() || $this->canClone() || $this->canViewAudits();
	}

	public function extraActionButtons($row)
	{
		return [];
	}

	public function beforeStore($data)
	{
		return false;
	}

	public function storeMethod($id, $data)
	{
		try {
			DB::beginTransaction();
			$target = @$id ? $this->getModelInstance()->findOrFail($id) : $this->getModelInstance();
			foreach (array_keys($data["data"]) as $key) {
				$target->{$key} = $data["data"][$key];
			}
			$target->save();
			$controller = new ResourceController;
			$controller->storeUploads($target, $data["upload"]);
			DB::commit();
			if (!request("input_origin")) {
				Messages::send("success", "Registro salvo com sucesso !!");
				if (request("clicked_btn") == "save") {
					$route = route('resource.edit', ["resource" => $this->id, "code" => $target->code]);
				} else {
					$route = route('resource.index', ["resource" => $this->id]);
				}
				return ["success" => true, "route" => $route, "model" => $target];
			} else {
				return ["success" => true, 'model' => $target];
			}
		} catch (\Exception $e) {
			DB::rollBack();
			Messages::send("error", "Erro ao salvar registro !!");
			return ["success" => false, "error" => $e->getMessage()];
		}
	}

	public function cloneMethod($id)
	{
		$content = $this->getModelInstance()->findOrFail($id);
		$cloned = $this->getModelInstance();
		$content_data = $content->toArray();
		unset($content_data["id"]);
		$cloned->fill($content_data);
		$cloned->save();
		return [
			"origin_id" => $id,
			"cloned_id" => $cloned->id,
			"success" => true,
			"route" => $this->id . "/" . $cloned->code . "/edit",
		];
	}

	public function getModelInstance()
	{
		return (is_string($this->model) ? app()->make($this->model)() : new $this->model());
	}

	public function breadcrumbLabels()
	{
		return [
			"list" => "{$this->label()}",
			"create" => "Cadastro de {$this->singularLabel()}",
			"view" => "Visualização de {$this->singularLabel()}",
			"report" => "Relatório de {$this->singularLabel()}",
			"edit" => "Edição de {$this->singularLabel()}",
			"import" => "Importação de {$this->singularLabel()}"
		];
	}

	public function isCreating()
	{
		return request()->page_type === "create";
	}

	public function isEditing()
	{
		return request()->page_type === "edit";
	}

	public function makeAclResource()
	{
		return [
			"can_view" => $this->canView(),
			"can_viewlist" => $this->canViewList(),
			"can_update" => $this->canUpdate(),
			"can_destroy" => $this->canDelete(),
			"can_create" => $this->canCreate(),
			"can_view_audits" => $this->canViewAudits(),
		];
	}

	public function serialize($page_type = null)
	{
		return [
			"label" => $this->label(),
			"singular_label" => $this->singularLabel(),
			"page_type" => $page_type,
			"breadcrumb_labels" => $this->breadcrumbLabels(),
			"first_btn" => $this->firstCrudBtn(),
			"second_btn" => $this->secondCrudBtn(),
			"icon" => $this->icon(),
			"acl" => $this->makeAclResource(),
			"after_create_slot" => $this->afterCreateSlot(),
			"after_edit_slot" => $this->afterEditSlot(),
			"before_edit_slot" => $this->beforeEditSlot(),
			"before_create_slot" => $this->beforeCreateSlot(),
			"dialog_sub_titles" => $this->dialogSubTitles(),
		];
	}

	public function dialogSubTitles()
	{
		return [
			"create" => false,
			"edit" => false,
		];
	}

	public function createMethod($params, $data)
	{
		$resource = $this;
		return view("vStack::resources.crud", compact("resource", "data", "params"));
	}

	public function editMethod($params, $data, $content)
	{
		$resource = $this;
		return view("vStack::resources.crud", compact("resource", "data", "params", "content"));
	}

	public function viewMethod($params, $data, $content)
	{
		$resource = $this;
		return view("vStack::resources.crud", compact("resource", "data", "params", "content"));
	}

	public function makeViewContent($data)
	{
		return view('vStack::resources.partials._crud_content', $data);
	}

	public function makeCreateContent($data)
	{
		return view('vStack::resources.partials._crud_content', $data);
	}

	public function makeEditContent($data)
	{
		return view('vStack::resources.partials._crud_content', $data);
	}

	public function secondViewBtn()
	{
		if ($this->canUpdate()) {
			return false;
		}
		return [
			"field" => "edit",
			"class" => "secondary",
			"content" => "<div class='flex '>
							<i class='el-icon-edit mr-2'></i>
							Editar
						</div>"
		];
	}

	public function firstViewBtn()
	{
		return [
			"field" => "back",
			"class" => "primary",
			"content" => "<div class='flex '>
							<i class='el-icon-arrow-left mr-2'></i>
							 Voltar
						</div>"
		];
	}

	public function prepareImportData($data)
	{
		return ["success" => true, "data" => []];
	}

	public function importMethod($data)
	{
		extract($data);
		$importer = new GlobalImporter($filepath, ResourceController::class, 'sheetImportRow', compact('config', 'extra_data', 'resource', 'fieldlist', 'filepath', 'tenant_id'));
		Excel::import($importer, $importer->getFile());
		$result = $importer->getResult();
		unlink($filepath);
		$success = $result["success"];
		$message = "";
		if (@$result["success"]) {
			$message = "Foi importado com sucesso sua planilha de " . $resource->label() . ". (" . $result['qty'] . " Registro" . ($result['qty'] > 1 ? 's' : '') . ")";
		} else {
			$message = "Erro na importação de planilha de " . $resource->label() . " ( " . $result["error"]['message'] . " )";
		}

		event(new WebSocketEvent("App.User." . $user->getIdAttribute(), "user.notification", [
			"type" => $success ? "success" : "error",
			"message" => $message
		]));

		return ["success" => $success, "message" => $message];
	}

	public function importRowMethod($new, $extra_data)
	{
		$fill_data = array_merge($new, $extra_data ? $extra_data : []);
		$new_model = @$new["id"] ? $this->getModelInstance()->findOrFail($new["id"]) : $this->getModelInstance();
		$keys = array_keys($fill_data);
		foreach ($keys as $key) {
			$new_model->{$key} = $fill_data[$key];
		}
		$new_model->save();
		return $new_model;
	}

	public function importerColumns()
	{
		return false;
	}

	public function exportingMessage()
	{
		return "O Download do seu relatório <b>iniciará automaticamente</b> após o processamento finalizar, <b>não feche
		está aba</b>, caso contrário o processo será perdido.";
	}

	public function prepareQueryToList($query)
	{
		return $query;
	}

	public function prepareQueryToExport($query)
	{
		return $query;
	}

	public function listItemsContent($data)
	{
		return false;
	}

	public function resourceTreePerPage()
	{
		return 10;
	}

	public function resourceTreeLoadItemsFilter(Request $request, $query)
	{
		$query = $query->where(@$request->label_index ?? "name", "like", "%{$request->filter}%");
		return  $query;
	}

	public static function getStaticMethod($method, $params = [])
	{
		$resource = new static();
		return $resource->{$method}($params);
	}

	public function qtyShowingButtons()
	{
		$enabled = array_filter([$this->canView(), $this->canUpdate(), $this->canDelete(), $this->canClone()]);
		$extras = $this->extraActionButtons(null);
		return count($enabled) + count($extras);
	}

	public function resourceLoadingSaveMassage($type)
	{
		return $type == "action" ? "Executando ..." : "Salvando ...";
	}

	public function canViewAudits()
	{
		return is_callable($this?->model?->isAuditable) ?  $this->model->isAuditable() : false;
	}

	public function canViewAuditsRow($row)
	{
		return $this->canViewAudits();
	}
}
