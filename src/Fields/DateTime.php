<?php

namespace marcusvbda\vstack\Fields;

class DateTime extends Field
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
		if (@$this->options["hide"]) {
			return $this->view = "";
		}

		if ($type == "view") {
			return $this->getViewOnlyValue();
		}

		$label          = $this->options["label"];
		$append         = @$this->options["append"];
		$prepend        = @$this->options["prepend"];
		$type           = $this->options["type"];
		$field          = $this->options["field"];
		$mask           = json_encode($this->options["mask"]);
		$end_placeholder   = @$this->options["end_placeholder"];
		$placeholder = @$this->options["placeholder"];
		$start_placeholder = @$this->options["start_placeholder"];
		$disabled       = @$this->options["disabled"] ? "true" : "false";
		$description    = $this->options["description"];
		$visible        = $this->options["visible"] ? 'true' : 'false';
		if ($type == "date" || $type == "daterange") {
			$format         = @$this->options["format"] ? $this->options["format"] : 'dd/MM/yyyy';
			$value_format   = @$this->options["value_format"] ? $this->options["value_format"] : 'yyyy-MM-dd';
		} else {
			$format         = @$this->options["format"] ? $this->options["format"] : 'dd/MM/yyyy HH:mm:ss';
			$value_format   = @$this->options["value_format"] ? $this->options["value_format"] : 'yyyy-MM-dd HH:mm:ss';
		}

		return $this->view = view("vStack::resources.field.datetime", compact(
			"disabled",
			"label",
			"prepend",
			"append",
			"mask",
			"description",
			"type",
			"field",
			"start_placeholder",
			"end_placeholder",
			"placeholder",
			"visible",
			"field",
			"format",
			"value_format"
		))->render();
	}
}