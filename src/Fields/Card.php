<?php

namespace marcusvbda\vstack\Fields;

class Card
{
	public $view;
	public $label;
	public $advanced;
	public $inputs;
	public $icon;
	public $_uid;

	public function __construct($label, $inputs, $options = false)
	{
		$this->label = $label;
		$this->inputs = $inputs;
		if (is_array($options)) {
			foreach ($options as $key => $value) {
				$this->{$key} = $value;
			}
		} else {
			$this->advanced = $options;
		}
		$this->_uid = uniqid();
		return $this->makeView();
	}

	private function mapInputs()
	{
		$views = collect($this->inputs)->map(function ($x) {
			return $x->getView();
		})->toArray();
		return implode("", $views);
	}

	private function makeView()
	{
		$index = $this->_uid;
		$label = $this->label;
		$inputs = $this->mapInputs();
		$advanced = $this->advanced;
		return $this->view = view("vStack::resources.field.card", compact(
			"label",
			"inputs",
			"advanced",
			"index"
		))->render();
	}
}