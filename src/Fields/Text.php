<?php

namespace marcusvbda\vstack\Fields;

class Text extends Field
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
		$append         = @$this->options["append"];
		$prepend        = @$this->options["prepend"];
		$type           = $this->options["type"];
		$field          = $this->options["field"];
		$mask           = json_encode($this->options["mask"]);
		$placeholder    = $this->options["placeholder"];
		$step           = @$this->options["step"] ?? 1;
		$disabled       = @$this->options["disabled"] ? "true" : "false";
		$description    = $this->options["description"];
		$visible        = $this->options["visible"] ? 'true' : 'false';
		$maxlength      = $this->options["maxlength"] ?  $this->options["maxlength"] : 255;
		$eval = " " . (@$this->options["eval"] ? trim($this->options["eval"]) : "") . " ";


		return $this->view = view("vStack::resources.field.text", compact(
			"disabled",
			"label",
			"prepend",
			"append",
			"mask",
			"description",
			"type",
			"step",
			"field",
			"placeholder",
			"visible",
			"field",
			"maxlength",
			"eval"
		))->render();
	}
}
