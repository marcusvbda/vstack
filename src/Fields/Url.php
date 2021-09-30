<?php

namespace marcusvbda\vstack\Fields;

class Url extends Field
{
    public $options = [];
    public $view = "";
    public function __construct($op = [])
    {
        $this->options = $op;
        parent::processFieldOptions();
    }

    public function getView($card_rules = [])
    {
        if (@$this->options["hide"]) {
            return $this->view = "";
        }

        $label          = $this->options["label"];
        $append         = @$this->options["append"];
        $prepend        = @$this->options["prepend"];
        $type           = "url";
        $field          = $this->options["field"];
        $mask           = json_encode($this->options["mask"]);
        $placeholder    = $this->options["placeholder"];
        $disabled       = @$this->options["disabled"] ? "true" : "false";

        return $this->view = view("vStack::resources.field.text", compact(
            "disabled",
            "label",
            "prepend",
            "append",
            "mask",
            "type",
            "field",
            "placeholder"
        ))->render();
    }
}