<?php

namespace marcusvbda\vstack\Fields;

class Card
{
	public $view;
	public $label;
	public $advanced;
	public $description;
	public $inputs;
	public $icon;
	public $_uid;

	public function __construct($label, $inputs, $options = false)
	{
		$this->label = is_array($label) ? $label[0] : $label;
		$this->description = is_array($label) ? $label[1] : "";
		$this->inputs = $inputs;
		if (is_array($options)) {
			foreach ($options as $key => $value) {
				$this->{$key} = $value;
			}
		} else {
			$this->advanced = $options;
		}
		$this->_uid = @$this->_uid ? $this->_uid : uniqid();
		return $this->makeView();
	}

	private function isViewType()
	{
		return request()->get("page_type") == "view";
	}

	private function mapInputs()
	{
		$isViewType = $this->isViewType();
		$views = collect($this->inputs)->map(function ($x) use ($isViewType) {
			return $x->getView($isViewType ? "view" : "input");
		})->toArray();
		return implode("", $views);
	}

	public function makeView()
	{
		$index = $this->_uid;
		$label = $this->label;
		$description = $this->description;
		$inputs = $this->mapInputs();
		$advanced = $this->advanced;
		$eval = " " . (@$this->options["eval"] ? trim($this->options["eval"]) : "") . " ";

		return $this->view = view("vStack::resources.field.card", compact(
			"label",
			"description",
			"inputs",
			"advanced",
			"index",
			"eval"
		))->render();
	}
}
