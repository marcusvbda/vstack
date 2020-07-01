<?php

namespace marcusvbda\vstack\Fields;

class BelongsToMany extends Field
{
    public $options = [];
    public $view = "";
    public function __construct($op = [])
    {
        $this->options = $op;
        $this->options["type"] = "belongsToMany";
        parent::processFieldOptions();
    }

    public function getView()
    {
        if (@$this->options["hide"]) return $this->view = "";

        $model       = $this->options["model"];
        $field       = @$this->options["field"];
        $label       = $this->options["label"];
        $disabled    = @$this->options["disabled"] ? "true" : "false";
        $route_list  = route("resource.inputs.option_list");
        $placeholder = $this->options["placeholder"];
        $required = $this->options["required"] ? "true" : "false";
        $description = $this->options["description"];
        $visible        = $this->options["visible"] ? 'true' : 'false';


        return $this->view = view("vStack::resources.field.belongstomany", compact(
            "visible",
            "required",
            "field",
            "label",
            "model",
            "disabled",
            "description",
            "placeholder",
            "route_list"
        ))->render();
    }
}
