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
		@$this->options["maxlength"]            = @$this->options["maxlength"] ?? 0;
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
		@$this->options["multiple"]             = @$this->options["multiple"] ?? false;
		@$this->options["_uid"]             	= @$this->options["_uid"] ? $this->options["_uid"] : uniqid();
		@$this->options["resource"]             = @$this->options["resource"] ? $this->options["resource"] : null;
		$this->checkRequired();
	}

	private function checkRequired()
	{
		if (@$this->options['type'] != "check") {
			$rules = !is_array($this->options['rules']) ? explode("|", $this->options['rules']) : $this->options['rules'];
			if ($this->options["required"] || $this->hasRequiredRule($rules)) {
				if ($this->options["required"]) {
					$this->addRequireRule($rules);
				} else {
					$this->options["required"] = true;
				}
				$this->options["label"] = $this->options["label"] . ' <small class="text-danger" style="position: relative;top: -2px;">*</small>';
			} else {
				if ($this->hasRequiredIfRule($rules)) {
					$this->options["label"] = $this->options["label"] . ' <small class="text-success" style="position: relative;top: -2px;">*</small>';
				} else {
					$this->options["label"] = $this->options["label"] . ' <small class="text-muted" style="position: relative;top: -2px;">(opcional)</small>';
				}
			}
		}
	}

	private function hasRequiredIfRule($rules)
	{
		foreach ($rules as $rule) {
			if (is_string($rule) && str_contains($rule, "required_if")) {
				return true;
			}
		}
		return false;
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

	protected function getViewOnlyValue()
	{
		$content = request()->get("content");
		$label  = @$this->options["label"];
		$description    = @$this->options["description"];
		$type    = @$this->options["type"];
		$model    = @$this->options["model"];
		$value = @$this->options["default"];
		$options = @$this->options["options"] ?? [];
		if ($content && @$this->options["field"]) {
			$value = @$content->{$this->options["field"]};
		}
		if (!$value) {
			$value = @$this->options["default"];
		}

		if ($type == "date" || $type == "daterange") {
			$format         = @$this->options["format"] ? $this->options["format"] : 'DD/MM/YYYY';
		} else {
			$format         = @$this->options["format"] ? $this->options["format"] : 'DD/MM/YYYY HH:mm:ss';
		}
		$eval = " " . (@$this->options["eval"] ? trim($this->options["eval"]) : "") . " ";

		return $this->view = view("vStack::resources.field.view_content", compact(
			"label",
			"description",
			"value",
			"type",
			"options",
			"model",
			"format",
			"eval"
		))->render();
	}

	public function getDefaultMaxlength($default)
	{
		$rules = data_get($this->options, 'rules', []);
		if (!is_array($rules)) {
			$rules = explode("|", $rules);
		}
		$rules = array_filter($rules);
		foreach ($rules as $rule) {
			if (gettype($rule) == "string") {
				if (strpos($rule, "max") !== false) {
					$number = data_get(explode(":", $rule), '1', $default);
					return $number;
				}
			}
		}
		return "255";
	}
}
