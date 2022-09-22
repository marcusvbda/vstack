<?php

namespace marcusvbda\vstack\Fields;

class BelongsTo extends Field
{
	public $options = [];
	public $view = "";
	public function __construct($op = [])
	{
		$this->options = $op;
		$this->options["type"] = "belongsTo";
		parent::processFieldOptions();
	}

	public function getView($type = "input")
	{
		if (@$this->options["hide"]) {
			return $this->view = "";
		}

		if ($type == "view") {
			return $this->getViewOnlyValue();
		}
		$allow_create = @$this->options["allow_create"] ? 'true' : 'false';
		$model       = @$this->options["model"] ? $this->options["model"] : null;
		$field       = @$this->options["field"];
		$multiple    = @$this->options["multiple"] ? @$this->options["multiple"] : false;
		$label       = $this->options["label"];
		$disabled    = @$this->options["disabled"] ? "true" : "false";
		$route_list  = route("resource.inputs.option_list");
		$placeholder = $model ? $this->options["placeholder"] : null;
		$options     = @$this->options["options"] ? json_encode($this->options["options"]) : "[]";
		$type     = @$this->options["type"] ? $this->options["type"] : ($multiple ? 'radio' : 'select');
		$description = $this->options["description"];
		$model_fields = @$this->options["model_fields"] ?? ["id" => "id", "name" => "name"];
		$visible        = $this->options["visible"] ? 'true' : 'false';
		$all_options_label = @$this->options["all_options_label"] ? $this->options["all_options_label"] : 'Todos(as)';
		$eval = " " . (@$this->options["eval"] ? trim($this->options["eval"]) : "") . " ";
		$model_filter = @$this->options["model_filter"] ? json_encode($this->options["model_filter"]) : "[]";
		return $this->view = view("vStack::resources.field.belongsto", compact(
			"field",
			"model",
			"label",
			"disabled",
			"description",
			"visible",
			"placeholder",
			"route_list",
			"options",
			"multiple",
			"all_options_label",
			"eval",
			"model_fields",
			"allow_create",
			"model_filter",
			"type"
		))->render();
	}
}
