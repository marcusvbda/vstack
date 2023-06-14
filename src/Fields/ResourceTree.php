<?php

namespace marcusvbda\vstack\Fields;

class ResourceTree extends Field
{
    public $options = [];
    public function __construct($op = [])
    {
        $this->options = $op;
        $this->options["type"] = "resource-tree";
        parent::processFieldOptions();
    }

    public function getView($type = "input")
    {
        if (@$this->options["hide"]) {
            return $this->view = "";
        }

        $resource = $this->options["resource"];
        $disabled = @$this->options["disabled"] ? "true" : "false";
        $relation = @$this->options['relation'];
        $parent_resource = @$this->options['parent_resource'];
        $eval = @$this->options["eval"]  ? $this->options["eval"] : '';
        $slot_top = @$this->options["slot_top"] ? $this->options["slot_top"] : "";
        $slot_bottom = @$this->options["slot_bottom"] ? $this->options["slot_bottom"] : "";

        return $this->view = view("vStack::resources.field.resource_tree", compact(
            "resource",
            "disabled",
            "eval",
            "relation",
            "parent_resource",
            "slot_top",
            "slot_bottom"
        ))->render();
    }
}
