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
        if ($type == "view") {
            return $this->getViewOnlyValue();
        }

        if (@$this->options["hide"]) {
            return $this->view = "";
        }

        $resource = $this->options["resource"];
        $disabled = @$this->options["disabled"] ? "true" : "false";
        $relation = @$this->options['relation'];
        $parent_resource = @$this->options['parent_resource'];
        $eval = @$this->options["eval"]  ? $this->options["eval"] : '';

        return $this->view = view("vStack::resources.field.resource_tree", compact(
            "resource",
            "disabled",
            "eval",
            "relation",
            "parent_resource",
        ))->render();
    }
}
