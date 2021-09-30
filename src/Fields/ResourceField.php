<?php

namespace marcusvbda\vstack\Fields;

class ResourceField extends Field
{
    public $options = [];
    public $view = "";
    public function __construct($op = [])
    {
        $this->options = $op;
        $this->options["type"] = "resource-field";
        parent::processFieldOptions();
    }

    public function getView()
    {
        if (@$this->options["hide"]) return $this->view = "";
        $resource = @$this->options["resource"] ? $this->options["resource"] : "";
        $this->options["field"] = $resource;

        return $this->view = view("vStack::resources.field.resource_field", compact("resource"))->render();
    }
}