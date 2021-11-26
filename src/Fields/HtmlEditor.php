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
		$mode = @$this->options["mode"] ? $this->options["mode"] : 'both';
		$show_btns = @$this->options["show_btns"] !== null ? $this->options["show_btns"] : true;
		$field = $this->options["field"];
		$placeholder = @$this->options["placeholder"] ? $this->options["placeholder"] : "Digite seu conteúdo markdown ou html";
		$direction = @$this->options["direction"] ? $this->options["direction"] : "row";
		$default = $this->options["default"];
		$eval = " " . (@$this->options["eval"] ? trim($this->options["eval"]) : "") . " ";

		return $this->view = view("vStack::resources.field.html_editor", compact(
			"description",
			"label",
			"field",
			"mode",
			"default",
			"show_btns",
			"placeholder",
			"direction",
			"eval"
		))->render();
	}
}
