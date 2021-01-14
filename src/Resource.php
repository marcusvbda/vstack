<?php

namespace marcusvbda\vstack;

use App;
use marcusvbda\vstack\Fields\{Card, Text};

class Resource
{
	public $model          = "";
	public $id             = [];

	public function __construct()
	{
		$this->model = App::make($this->model);
		$this->makeId();
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

	public function export_columns()
	{
		return ["id", "created_at"];
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

	public function canViewList()
	{
		return true;
	}

	public function canView()
	{
		return true;
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
				$validation_rules[$field->options["field"]] = $field->options["rules"];
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
						$validation_messages[@$field->options["field"] . "." . $key] = @$value;
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
			"pink",
			"#f115e0",
			"#7e15f1",
			"#02bec7",
			"#01a02a",
			"#deec0d"
		];
	}

	public function getColumnIndex($columns, $row, $key, $placeholder = "          -          ")
	{
		$value = "";
		if (!@$columns[$key]["handler"]) {
			$value = $row;
			$_runner = explode("->", $key);
			foreach ($_runner as $idx) $value = @$value->{$idx};
			return ($value ? $value : $placeholder);
		}
		return  $columns[$key]["handler"]($row);
	}

	public function actions()
	{
		return [];
	}

	public function viewListBlade()
	{
		return "vStack::resources.partials._default_table";
	}
}