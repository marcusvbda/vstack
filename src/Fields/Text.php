<?php

namespace marcusvbda\vstack\Fields;

class Text extends Field
{
    public $options = [];
    public $view = "";
    public function __construct($op = [])
    {
        $this->options = $op;
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
        $description    = $this->options["description"];
        $visible        = $this->options["visible"] ? 'true' : 'false';
        if (!@$this->options["hide"])
            $view = "<v-input class='mb-3'  
                        :disabled='$disabled'                                                                  
                        label='$label'                                                                   
                        prepend='$prepend'                                                               
                        append='$append'                                                               
                        append='$append' 
                        :mask='$mask'                                                              
                        description='$description'                                                              
                        type='$type'                                                                     
                        v-model='form.$field'                                                            
                        placeholder='$placeholder'   
                        v-show='$visible'                                                    
                        :errors='errors.$field ? errors.$field : false'                          
                    />";
        return $this->view = $view;
    }
}
