<?php

namespace marcusvbda\vstack\Fields;

class HtmlEditor extends Field
{
	public $options = [];
	public $view = "";
	public function __construct($op = [])
	{
		$this->options = $op;
		$this->options["type"] = "html_editor";
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

		$field     = $this->options["field"];
		$label = $this->options["label"];
		$description = $this->options["description"];
		$mode = @$this->options["mode"] ? $this->options["mode"] : 'webpage';
		$field = $this->options["field"];
		$default = $this->options["default"];
		$eval = " " . (@$this->options["eval"] ? trim($this->options["eval"]) : "") . " ";
		$blocks = @$this->options["blocks"] ? $this->options["blocks"]  : (object)[];
		$settings = @$this->options["settings"] ? (object)$this->options["settings"]  : (object)[];
		$height = @$this->options["height"] ? $this->options["height"]  : 1000;
		$slot_top = @$this->options["slot_top"] ? $this->options["slot_top"] : "";
		$slot_bottom = @$this->options["slot_bottom"] ? $this->options["slot_bottom"] : "";

		return $this->view = view("vStack::resources.field.html_editor", compact(
			"description",
			"label",
			"field",
			"mode",
			"default",
			"eval",
			"blocks",
			"settings",
			"height",
			"slot_top",
			"slot_bottom"
		))->render();
	}
}
