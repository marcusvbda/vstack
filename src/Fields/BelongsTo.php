<?php

namespace marcusvbda\vstack\Fields;

class BelongsTo extends Field
{
    public $options = [];
    public $view = "";
    public function __construct($op = [])
    {
        $this->options = $op;
        $this->options["type"] = "belongsTo";
        parent::processFieldOptions();
    }

    public function getView()
    {
        $view = "";
        $model       = @$this->options["model"] ? $this->options["model"] : null;
        $field       = @$this->options["field"];
        $label       = $this->options["label"];
        $disabled    = @$this->options["disabled"] ? "true" : "false";
        $route_list  = route("resource.inputs.option_list");
        $placeholder = $model ? $this->options["placeholder"] : null;
        $options     = @$this->options["options"] ? json_encode($this->options["options"]) : "[]";
        if (!@$this->options["hide"])
            $view = "<v-select v-model='form.$field' list_model='$model' label='$label' :disabled='$disabled'                 
                        placeholder='$placeholder' route_list='$route_list' :option_list='$options' :errors='errors.$field ? errors.$field : false'    
                    />";
        return $this->view = $view;
    }
}
