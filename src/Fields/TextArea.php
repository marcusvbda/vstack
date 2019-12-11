<?php

namespace marcusvbda\vstack\Fields;

class TextArea extends Field
{
    public $options = [];
    public $view = "";
    public function __construct($op = [])
    {
        $this->options = $op;
        $this->options["type"] = "textarea";
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
        $rows           = @$this->options["rows"] ? $this->options["rows"] : 3;
        if (!@$this->options["hide"])
            $view = "<v-input-textarea class='mb-3'  
                        :disabled='$disabled'                                                                  
                        label='$label'                                                                     
                        type='$type'           
                        :rows='$rows'                                                          
                        v-model='form.$field'                                                            
                        placeholder='$placeholder'                                                       
                        :errors='errors.$field ? errors.$field : false'                                  
                    />";
        return $this->view = $view;
    }
}
