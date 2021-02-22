<?php

namespace marcusvbda\vstack\Fields;

class CustomComponent extends Field
{
	public $options = [];
	public $view = "";
	public function __construct($view = "", $options = [])
	{
		$this->options = $options;
		$this->view = $view;
		$this->options["type"] = "custom_component";
	}

	public function getView()
	{
		$view = @$this->view;
		return $view;
	}
}
