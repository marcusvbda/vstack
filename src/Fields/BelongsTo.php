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

	public function getView()
	{
		if (@$this->options["hide"]) return $this->view = "";

		$model       = @$this->options["model"] ? $this->options["model"] : null;
		$field       = @$this->options["field"];
		$multiple       = @$this->options["multiple"] ? @$this->options["multiple"] : false;
		$label       = $this->options["label"];
		$disabled    = @$this->options["disabled"] ? "true" : "false";
		$route_list  = route("resource.inputs.option_list");
		$placeholder = $model ? $this->options["placeholder"] : null;
		$options     = @$this->options["options"] ? json_encode($this->options["options"]) : "[]";
		$description = $this->options["description"];
		$visible        = $this->options["visible"] ? 'true' : 'false';

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
			"multiple"
		))->render();
	}
}