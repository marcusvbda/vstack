<?php

namespace marcusvbda\vstack;

use App;
use marcusvbda\vstack\Controllers\ResourceController;
use marcusvbda\vstack\Fields\{Card, Text};
use marcusvbda\vstack\Models\Migration;
use marcusvbda\vstack\Services\Messages;

class Resource
{
	public $model = null;
	public $model_string  = null;
	public $debug = false;
	public $id    = [];

	public function __construct()
	{
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
		return [10, 20, 50, 100];
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

	public function storeButtonlabel()
	{
		return "<span class='el-icon-plus mr-2'></span>Cadastrar";
	}

	public function importButtonlabel()
	{
		return "<span class='el-icon-upload2 mr-2'></span>Importar Planilha de " . $this->label();
	}

	public function exportButtonlabel()
	{
		return "<span class='el-icon-download mr-2'></span>Exportar Relatório de " . $this->label();
	}

	public function noResultsFoundText()
	{
		return "Nenhum resultado encontrado";
	}

	public function resultsFoundLabel()
	{
		return "Resultados encontrados : ";
	}

	public function nothingStoredText()
	{
		return "<h4>Nada cadastrado ainda...<h4>";
	}

	public function secondCrudBtn()
	{
		if ($this->crudType() == "dialog") {
			return false;
		}
		return [
			"size" => "small",
			"field" => "save",
			"type" => "success",
			"content" => "<div class='d-flex flex-row'>
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
			"type" => "info",
			"content" => "<div class='d-flex flex-row'>
							<i class='el-icon-arrow-left mr-2'></i>
							Salvar e Voltar
						</div>"
		];
	}

	public function nothingStoredSubText()
	{
		return "<span>Clique no botão abaixo para adicionar o primeiro registro ...</span>";
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

	public function export_columns($cx)
	{
		return [
			"id" => ["label" => "Código"],
			"created_at" => ["label" => "Data de Criação"],
		];
	}

	public function getTableColumns()
	{
		return $this->model->getConnection()->getSchemaBuilder()->getColumnListing($this->model->getTable());
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

	public function maxRowsExportSync()
	{
		return 1000;
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
		return true;
	}

	public function canDeleteRow($row)
	{
		return true;
	}

	public function beforeDelete()
	{
		return [];
	}

	public function canCloneRow($row)
	{
		return false;
	}

	public function canViewRow($row)
	{
		return true;
	}

	public function canView()
	{
		return true;
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
		return true;
	}

	public function listType()
	{
		return ["table"];
	}

	public function canUpdate()
	{
		return true;
	}

	public function canDelete()
	{
		return true;
	}

	public function canViewReport()
	{
		return false;
	}

	public function exportNotificationView()
	{
		return "vStack::resources.mails.export_notification";
	}

	public function getValidationRule()
	{
		$validation_rules = [];
		foreach ($this->fields() as $card) {
			foreach ($card->inputs as $field) {
				$rules = @$field->options["rules"] ?? [];
				if ($field->options["required"]) {
					$rules[] = "required";
				}
				$rules = is_array($rules) ? $rules : [$rules];
				$validation_rules[@$field->options["field"] ?? "*"] = array_filter($rules, function ($row) {
					return $row;
				});
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

	public function tagColors()
	{
		return [
			"red",
			"blue",
			"green",
			"purple",
			"brown",
			"orange",
			"#f115e0",
			"#7e15f1",
			"#02bec7",
			"#01a02a",
			"#deec0d"
		];
	}

	public function actions()
	{
		return [];
	}

	public function viewListBlade()
	{
		return "vStack::resources.partials._default_table";
	}

	public function indexBlade()
	{
		return "vStack::resources.partials._default_index";
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
		return $this->canView() || $this->canUpdate() || $this->canDelete() || $this->canClone();
	}

	public function storeMethod($id, $data)
	{
		$target = @$id ? $this->getModelInstance()->findOrFail($id) : $this->getModelInstance();
		foreach (array_keys($data["data"]) as $key) {
			$target->{$key} = $data["data"][$key];
		}
		$target->save();
		$controller = new ResourceController;
		$controller->storeUploads($target, $data["upload"]);
		Messages::send("success", "Registro salvo com sucesso !!");
		if (request("clicked_btn") == "save") {
			$route = route('resource.edit', ["resource" => $this->id, "code" => $target->code]);
		} else {
			$route = route('resource.index', ["resource" => $this->id]);
		}
		return ["success" => true, "route" => $route, "model" => $target];
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
			"clone" => "Clonagem de {$this->singularLabel()}"
		];
	}

	public function serialize($page_type)
	{
		return [
			"label" => $this->label(),
			"singular_label" => $this->singularLabel(),
			"page_type" => $page_type,
			"breadcrumb_labels" => $this->breadcrumbLabels(),
			"first_btn" => $this->firstCrudBtn(),
			"second_btn" => $this->secondCrudBtn(),
			"acl" => ["can_update" => $this->canUpdate()],
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

	public function crudType()
	{
		return "page";
	}

	public function crudRightCardBody()
	{
		return null;
	}

	public function createMethod($params, $data)
	{
		if ($this->crudType() == "dialog") {
			return abort(404);
		}
		$resource = $this;
		return view("vStack::resources.crud", compact("resource", "data", "params"));
	}

	public function editMethod($params, $data, $content)
	{
		if ($this->crudType() == "dialog") {
			return  abort(404);
		}
		$resource = $this;
		return view("vStack::resources.crud", compact("resource", "data", "params", "content"));
	}
}