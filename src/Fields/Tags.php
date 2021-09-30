<?php

namespace marcusvbda\vstack\Fields;

class Tags extends Field
{
    public $options = [];
    public $view = "";
    public function __construct($op = [])
    {
        $this->options = $op;
        $this->options["type"] = "tags";
        parent::processFieldOptions();
    }

    public function getView()
    {
        if (@$this->options["hide"]) return $this->view = "";
        $label          = $this->options["label"];
        $field          = $this->options["field"];
        $disabled       = @$this->options["disabled"] ? "true" : "false";
        $description    = $this->options["description"];

        return $this->view = view("vStack::resources.field.tags", compact(
            "disabled",
            "label",
            "description",
            "field"
        ))->render();
    }
}