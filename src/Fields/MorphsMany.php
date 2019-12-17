<?php

namespace marcusvbda\vstack\Fields;

class MorphsMany extends Field
{
    public $options = [];
    public $view = "";
    public function __construct($op = [])
    {
        $this->options = $op;
        $this->options["type"] = "morphsMany";
        parent::processFieldOptions();
    }

    public function getView()
    {
        $view = "";
        $field       = @$this->options["field"];
        $label       = $this->options["label"];
        $unique      = @$this->options["unique"] ? $this->options["unique"] : "true";
        $disabled    = @$this->options["disabled"] ? "true" : "false";
        $placeholder = $this->options["placeholder"];
        $required = $this->options["required"] ? "true": "false";
        if (!@$this->options["hide"])
            $view = "<v-tags multiple :allowcreate='true' :required='$required' v-model='form.$field' label='$label' :disabled='$disabled' :unique='$unique'                    
                        placeholder='$placeholder' :errors='errors.$field ? errors.$field : false'   
                    />";
        return $this->view = $view;
    }
}
