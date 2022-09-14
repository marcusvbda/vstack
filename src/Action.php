<?php

namespace marcusvbda\vstack;

use Illuminate\Http\Request;

class Action
{
	public $id = "action-id";
	public $title = "action title";
	public $message = "action message";
	public $run_btn = "execute";

	public function __construct()
	{
		$this->id = strtolower(str_replace("\\", "-", static::class));
	}

	public function inputs()
	{
		return [];
	}

	public function handler(Request $request)
	{
		return ['success' => false, 'data' => $request->all(), "message" => "message here ..."];
	}

	public function submitButton()
	{
		$run_btn = $this->run_btn;
		return [
			"size" => "medium",
			"field" => "action_submit",
			"type" => "primary",
			"content" => $run_btn
		];
	}

	public function getValidationRule($card_index = "all")
	{
		$validation_rules = [];
		foreach ($this->inputs() as $key => $card) {
			if ($card_index === "all" || $key === $card_index) {
				$validation_rules = array_merge($validation_rules, $this->getCardFieldsRules($card));
			}
		}
		return $validation_rules;
	}

	private function getCardFieldsRules($card)
	{
		$validation_rules = [];

		foreach ($card->inputs as $field) {
			$rules = @$field->options["rules"] ?? [];
			if (!is_array($rules)) {
				$rules = explode("|", $rules);
			}
			if (@$field->options["required"]) {
				$rules[] = "required";
			}
			$rules = is_array($rules) ? $rules : [$rules];
			$validation_rules[@$field->options["field"] ?? "*"] = array_filter($rules, function ($row) {
				return $row;
			});
		}
		return $validation_rules;
	}
}
