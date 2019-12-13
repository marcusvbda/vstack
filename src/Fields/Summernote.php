<?php

namespace marcusvbda\vstack\Fields;

class Summernote extends Field
{
    public $options = [];
    public $view = "";
    public function __construct($op = [])
    {
        $this->options = $op;
        $this->options["type"] = "summernote";
        parent::processFieldOptions();
    }

    public function getView()
    {
        $view = "";
        $label          = $this->options["label"];
        $field          = $this->options["field"];
        $type           = $this->options["type"];
        $placeholder    = $this->options["placeholder"];
        $disabled       = @$this->options["disabled"] ? "true" : "false";
        $height         = @$this->options["height"] ? $this->options["height"] : 150;
        if (!@$this->options["hide"])
            $view = "<v-summernote class='mb-3'  
                        :disabled='$disabled'                                                                  
                        label='$label'                                                                     
                        type='$type'           
                        :height='$height'                                                        
                        v-model='form.$field'                                                            
                        placeholder='$placeholder'                                                       
                        :errors='errors.$field ? errors.$field : null'                                  
                    />";
        return $this->view = $view;
    }
}
