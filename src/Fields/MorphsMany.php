<?php

namespace marcusvbda\vstack\Fields;

class MorphsMany extends Field
{
    public $options = [];
    public $view = "";
    public function __construct($op = [])
    {
        $this->options = $op;
        $this->options["type"] = "morphsMany";
        parent::processFieldOptions();
    }

    public function getView()
    {
        if (@$this->options["hide"])  return $this->view = "";
        $field       = @$this->options["field"];
        $label       = $this->options["label"];
        $unique      = @$this->options["unique"] ? $this->options["unique"] : "true";
        $disabled    = @$this->options["disabled"] ? "true" : "false";
        $placeholder = $this->options["placeholder"];
        $required = $this->options["required"] ? "true" : "false";

        return $this->view = view("vStack::resources.morphsmany", compact(
            "required",
            "field",
            "label",
            "disabled",
            "unique",
            "placeholder"
        ))->render();
    }
}
