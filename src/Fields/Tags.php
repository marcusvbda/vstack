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

    public function getView($type = "input")
    {
        if (@$this->options["hide"]) {
            return $this->view = "";
        }

        $label          = $this->options["label"];
        $field          = $this->options["field"];
        $disabled       = @$this->options["disabled"] ? "true" : "false";
        $description    = $this->options["description"];
        $eval = " " . (@$this->options["eval"] ? trim($this->options["eval"]) : "") . " ";
        $slot_top = @$this->options["slot_top"] ? $this->options["slot_top"] : "";
        $slot_bottom = @$this->options["slot_bottom"] ? $this->options["slot_bottom"] : "";

        return $this->view = view("vStack::resources.field.tags", compact(
            "disabled",
            "label",
            "description",
            "field",
            "eval",
            "slot_top",
            "slot_bottom"
        ))->render();
    }
}
