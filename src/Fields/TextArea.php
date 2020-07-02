<?php

namespace marcusvbda\vstack\Fields;

class TextArea extends Field
{
    public $options = [];
    public $view = "";
    public function __construct($op = [])
    {
        $this->options = $op;
        $this->options["type"] = "textarea";
        parent::processFieldOptions();
    }

    public function getView()
    {
        if (@$this->options["hide"]) return $this->view = "";

        $label          = $this->options["label"];
        $field          = $this->options["field"];
        $type           = $this->options["type"];
        $placeholder    = $this->options["placeholder"];
        $disabled       = @$this->options["disabled"] ? "true" : "false";
        $rows           = @$this->options["rows"] ? $this->options["rows"] : 3;

        return $this->view = view("vStack::resources.field.textarea", compact(
            "disabled",
            "label",
            "type",
            "rows",
            "placeholder",
            "visible",
            "field"
        ))->render();
    }
}
