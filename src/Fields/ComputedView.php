<?php

namespace marcusvbda\vstack\Fields;

class ComputedView extends Field
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

		$label          = $this->options["label"];
		$description    = $this->options["description"];
		$eval = " " . (@$this->options["eval"] ? trim($this->options["eval"]) : "") . " ";
		$slot_top = @$this->options["slot_top"] ? $this->options["slot_top"] : "";
		$slot_bottom = @$this->options["slot_bottom"] ? $this->options["slot_bottom"] : "";
		$template = @$this->options["template"] ? $this->options["template"] : "<span>{{eval}}</span>";
		return $this->view = view("vStack::resources.field.computed_view", compact(
			"label",
			"description",
			"eval",
			"slot_top",
			"slot_bottom",
			"template"
		))->render();
	}
}
