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
		$label = @$this->options["label"];
		$description = @$this->options["description"];
		return "
			<tr>
				<td class='w-25'>
					<div class='d-flex flex-column'>
						<b class='input-title'>$label</b>
						<small class='text-muted mt-1'>$description</small>
					</div>
				</td>
				<td>
					$view
				</td>
			</tr>
		";
	}
}