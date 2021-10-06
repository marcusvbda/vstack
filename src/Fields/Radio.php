<?php

namespace marcusvbda\vstack\Fields;

class Radio extends Field
{
	public $options = [];
	public $view = "";
	public function __construct($op = [])
	{
		$this->options = $op;
		$this->options["type"] = "radio";
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

		$model       = @$this->options["model"] ? $this->options["model"] : null;
		$field       = @$this->options["field"];
		$label       = $this->options["label"];
		$disabled    = @$this->options["disabled"] ? "true" : "false";
		$options     = @$this->options["options"] ? json_encode($this->options["options"]) : "[]";
		$description = $this->options["description"];

		return $this->view = view("vStack::resources.field.radio", compact(
			"field",
			"model",
			"label",
			"disabled",
			"description",
			"options"
		))->render();
	}
}