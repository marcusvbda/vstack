<?php

namespace marcusvbda\vstack\Fields;

class Field
{
	public $options = [];
	public $view    = "";

	public function processFieldOptions()
	{
		@$this->options["type"]                 = @$this->options["type"] ?? "text";
		@$this->options["label"]                = @$this->options["label"] ?? "";
		@$this->options["field"]                = @$this->options["field"] ?? "";
		@$this->options["required"]             = @$this->options["required"] ?? false;
		@$this->options["placeholder"]          = @$this->options["placeholder"] ?? "";
		@$this->options["minlength"]            = @$this->options["minlength"] ?? 0;
		@$this->options["max"]                  = @$this->options["max"] ?? null;
		@$this->options["min"]                  = @$this->options["min"] ?? null;
		@$this->options["mask"]                 = @$this->options["mask"] ?? null;
		@$this->options["value"]                = @$this->options["value"] ?? null;
		@$this->options["default"]              = @$this->options["default"] ?? null;
		@$this->options["append"]               = @$this->options["append"] ?? null;
		@$this->options["prepend"]              = @$this->options["prepend"] ?? null;
		@$this->options["rules"]                = @$this->options["rules"] ?? '';
		@$this->options["mask"]                 = @$this->options["mask"] ?? '';
		@$this->options["description"]          = @$this->options["description"] ?? '';
		@$this->options["visible"]              = @$this->options["visible"] ?? true;
		$this->checkRequired();
	}

	private function checkRequired()
	{
		$rules = !is_array($this->options['rules']) ? explode("|", $this->options['rules']) : $this->options['rules'];
		if ($this->options["required"] || $this->hasRequiredRule($rules)) {
			if ($this->options["required"]) $this->addRequireRule($rules);
			else $this->options["required"] = true;
			$this->options["label"] = $this->options["label"] . ' <small class="text-danger" style="position: relative;top: -2px;">*</small>';
		} else {
			$this->options["label"] = $this->options["label"] . ' <small class="text-muted" style="position: relative;top: -2px;">(opcional)</small>';
		}
	}

	private function addRequireRule($rules)
	{
		if (!$this->hasRequiredRule($rules)) {
			$rules[] = "required";
			$this->options['rules'] = $rules;
		}
	}

	private function hasRequiredRule($rules)
	{
		return array_search("required", $rules) !== false;
	}
}
