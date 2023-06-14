<?php

namespace marcusvbda\vstack\Fields;

class Check extends Field
{
    public $options = [];
    public function __construct($op = [])
    {
        $this->options = $op;
        $this->options["type"] = "check";
        parent::processFieldOptions();
    }

    public function getView($type = "input")
    {
        if (@$this->options["hide"]) {
            return $this->view = "";
        }

        $label          = $this->options["label"];
        $field          = $this->options["field"];
        $active_color   = @$this->options["active_color"] ? $this->options["active_color"] : "green";
        $inactive_color = @$this->options["inactive_color"] ? $this->options["inactive_color"] : "red";
        $active_text    = @$this->options["active_text"] ? $this->options["active_text"] : "";
        $inactive_text  = @$this->options["inactive_text"] ? $this->options["inactive_text"] : "";
        $disabled       = @$this->options["disabled"] ? "true" : "false";
        $description    = @$this->options["description"];
        $eval = " " . (@$this->options["eval"] ? trim($this->options["eval"]) : "") . " ";
        $slot_top = @$this->options["slot_top"] ? $this->options["slot_top"] : "";
        $slot_bottom = @$this->options["slot_bottom"] ? $this->options["slot_bottom"] : "";

        return $this->view = view("vStack::resources.field.check", compact(
            "label",
            "description",
            "disabled",
            "field",
            "active_text",
            "inactive_text",
            "active_color",
            "inactive_color",
            "eval",
            "slot_top",
            "slot_bottom"
        ))->render();
    }
}
