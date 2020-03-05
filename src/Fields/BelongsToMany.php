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
        $view = "";
        $model       = $this->options["model"];
        $field       = @$this->options["field"];
        $label       = $this->options["label"];
        $disabled    = @$this->options["disabled"] ? "true" : "false";
        $route_list  = route("resource.inputs.option_list");
        $placeholder = $this->options["placeholder"];
        $required = $this->options["required"] ? "true" : "false";
        $description = $this->options["description"];
        $visible        = $this->options["visible"] ? 'true' : 'false';

        if (!@$this->options["hide"])
            $view = "<v-select multiple v-show='$visible' :required='$required' v-model='form.$field' list_model='$model' label='$label' :disabled='$disabled'  description='$description'                   
                        placeholder='$placeholder' route_list='$route_list' :errors='errors.$field ? errors.$field : false'   
                    />";
        return $this->view = $view;
    }
}
