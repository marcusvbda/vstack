<?php

namespace marcusvbda\vstack\Fields;

class TinyMce extends Field
{
	public $options = [];
	public $view = "";
	public function __construct($op = [])
	{
		$this->options = $op;
		parent::processFieldOptions();
	}

	public function getView($type = "input")
	{
		if ($type == "view") {
			return $this->getViewOnlyValue();
		}

		if (@$this->options["hide"]) {
			return $this->view = "";
		}

		$label          = $this->options["label"];
		$field          = $this->options["field"];
		$placeholder    = $this->options["placeholder"];
		$disabled       = @$this->options["disabled"] ? "true" : "false";
		$description    = $this->options["description"];
		$visible        = $this->options["visible"] ? 'true' : 'false';
		$maxlength      = $this->options["maxlength"] ?  $this->options["maxlength"] : $this->getDefaultMaxlength(255);
		$eval = " " . (@$this->options["eval"] ? trim($this->options["eval"]) : "") . " ";
		$show_value_length = (@$this->options["show_value_length"] !== null) ? $this->options["show_value_length"] : false;
		$show_value_length = $show_value_length ? 'true' : 'false';
		$height = @$this->options["height"] ?  $this->options["height"] : 300;
		return $this->view = view("vStack::resources.field.tiny", compact(
			"disabled",
			"label",
			"description",
			"field",
			"placeholder",
			"visible",
			"field",
			"maxlength",
			"height",
			"eval",
			"show_value_length"
		))->render();
	}
}
