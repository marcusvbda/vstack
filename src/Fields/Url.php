<?php

namespace marcusvbda\vstack\Fields;

class Url extends Field
{
    public $options = [];
    public $view = "";
    public function __construct($op = [])
    {
        $this->options = $op;
        $this->options["type"] = "url";
        parent::processFieldOptions();
    }

    public function getView()
    {
        $view = "";
        $label          = $this->options["label"];
        $append         = @$this->options["append"];
        $prepend        = @$this->options["prepend"];
        $type           = $this->options["type"];
        $field          = $this->options["field"];
        $mask           = json_encode($this->options["mask"]);
        $placeholder    = $this->options["placeholder"];
        $disabled       = @$this->options["disabled"] ? "true" : "false";
        if (!@$this->options["hide"])
            $view = "<v-input class='mb-3'  
                        :disabled='$disabled'                                                                  
                        label='$label'                                                                   
                        prepend='$prepend'                                                               
                        append='$append'                                                               
                        append='$append' 
                        :mask='$mask'                                                              
                        type='$type'                                                                     
                        v-model='form.$field'                                                            
                        placeholder='$placeholder'                                                       
                        :errors='errors.$field ? errors.$field : false'                                  
                    />";
        return $this->view = $view;
    }
}
